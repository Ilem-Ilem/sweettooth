-- SQL script to create product_dispatches table
-- Run this with: sqlite3 database/database.sqlite < create_product_dispatches_table.sql

CREATE TABLE IF NOT EXISTS "product_dispatches" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "branch_id" TEXT NOT NULL,
    "daily_produce_id" INTEGER,
    "production_shift_id" INTEGER NOT NULL,
    "product_id" TEXT NOT NULL,
    "dispatched_by" TEXT NOT NULL,
    "quantity" NUMERIC(12, 2) NOT NULL,
    "uom" VARCHAR(50) NOT NULL,
    "dispatch_time" DATETIME NOT NULL,
    "shift_type" VARCHAR(255) NOT NULL CHECK("shift_type" IN ('morning', 'afternoon', 'night')),
    "dispatch_date" DATE NOT NULL,
    "received_by" TEXT,
    "received_at" DATETIME,
    "status" VARCHAR(255) NOT NULL DEFAULT 'dispatched' CHECK("status" IN ('dispatched', 'received', 'rejected')),
    "notes" TEXT,
    "created_at" DATETIME,
    "updated_at" DATETIME,
    FOREIGN KEY("branch_id") REFERENCES "branches"("id") ON DELETE CASCADE,
    FOREIGN KEY("daily_produce_id") REFERENCES "daily_produces"("id") ON DELETE SET NULL,
    FOREIGN KEY("production_shift_id") REFERENCES "shifts"("id") ON DELETE CASCADE,
    FOREIGN KEY("product_id") REFERENCES "products"("id") ON DELETE CASCADE,
    FOREIGN KEY("dispatched_by") REFERENCES "employees"("id") ON DELETE CASCADE,
    FOREIGN KEY("received_by") REFERENCES "employees"("id") ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS "product_dispatches_product_id_dispatch_date_shift_type_index"
    ON "product_dispatches" ("product_id", "dispatch_date", "shift_type");

CREATE INDEX IF NOT EXISTS "product_dispatches_branch_id_dispatch_date_index"
    ON "product_dispatches" ("branch_id", "dispatch_date");
