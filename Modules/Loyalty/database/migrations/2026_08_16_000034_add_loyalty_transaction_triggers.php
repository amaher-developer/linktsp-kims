<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * kims_loyalty_transactions is the source of truth; kims_loyalty_accounts
 * (balance, lifetime_earned, lifetime_redeemed) is a cached projection kept
 * honest by these triggers. See kims_schema.sql section 10 for the full
 * rationale — the FOR UPDATE lock in the BEFORE INSERT trigger serializes
 * concurrent writes to the same account (e.g. a double-tap redeem) without
 * blocking writes to other accounts.
 *
 * The application must never compute balance_before/balance_after itself
 * and must never write kims_loyalty_accounts.balance directly.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_loyalty_txn_before_insert
            BEFORE INSERT ON kims_loyalty_transactions
            FOR EACH ROW
            BEGIN
                DECLARE current_balance INT;

                SELECT balance INTO current_balance
                FROM kims_loyalty_accounts
                WHERE id = NEW.loyalty_account_id
                FOR UPDATE;

                IF current_balance + NEW.points < 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Loyalty transaction would drive balance negative';
                END IF;

                SET NEW.balance_before = current_balance;
                SET NEW.balance_after  = current_balance + NEW.points;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_loyalty_txn_after_insert
            AFTER INSERT ON kims_loyalty_transactions
            FOR EACH ROW
            BEGIN
                UPDATE kims_loyalty_accounts
                SET balance           = NEW.balance_after,
                    lifetime_earned   = lifetime_earned + IF(NEW.points > 0, NEW.points, 0),
                    lifetime_redeemed = lifetime_redeemed + IF(NEW.points < 0, -NEW.points, 0)
                WHERE id = NEW.loyalty_account_id;
            END
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_loyalty_txn_before_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_loyalty_txn_after_insert');
    }
};
