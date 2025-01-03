PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);
INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2024_12_02_074049_create_test_models_table',1);
INSERT INTO migrations VALUES(5,'2024_12_03_131205_create_medias_table',1);
CREATE TABLE IF NOT EXISTS "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime);
INSERT INTO users VALUES(1,'Test User','test@example.org','2025-01-03 13:48:41','$2y$12$PGdibx6k/gmX.vGHN6SqWu8Hr5Q66De86tujAXFH3eJpFhdQzGB1i','7f4p80HBo6','2025-01-03 13:48:42','2025-01-03 13:48:42');
CREATE TABLE IF NOT EXISTS "password_reset_tokens" ("email" varchar not null, "token" varchar not null, "created_at" datetime, primary key ("email"));
CREATE TABLE IF NOT EXISTS "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null, primary key ("id"));
CREATE TABLE IF NOT EXISTS "cache" ("key" varchar not null, "value" text not null, "expiration" integer not null, primary key ("key"));
CREATE TABLE IF NOT EXISTS "cache_locks" ("key" varchar not null, "owner" varchar not null, "expiration" integer not null, primary key ("key"));
CREATE TABLE IF NOT EXISTS "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);
CREATE TABLE IF NOT EXISTS "job_batches" ("id" varchar not null, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" text not null, "options" text, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer, primary key ("id"));
CREATE TABLE IF NOT EXISTS "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS "test_models" ("id" integer primary key autoincrement not null, "autocomplete_local" integer, "autocomplete_remote" integer, "autocomplete_remote2" integer, "autocomplete_list" text, "check" tinyint(1), "date" date, "date_time" datetime, "time" time, "editor_html" text, "editor_html_localized" text, "editor_markdown" text, "geolocation" text, "list" text, "number" integer, "select_dropdown" integer, "select_checkboxes" integer, "select_radios" integer, "tags" text, "textarea" text, "textarea_localized" text, "text" varchar, "text_localized" text, "upload_id" integer, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "medias" ("id" integer primary key autoincrement not null, "model_type" varchar not null, "model_id" integer not null, "model_key" varchar, "file_name" varchar, "mime_type" varchar, "disk" varchar default 'local', "size" integer, "custom_properties" text, "order" integer, "created_at" datetime, "updated_at" datetime);
DELETE FROM sqlite_sequence;
INSERT INTO sqlite_sequence VALUES('migrations',5);
INSERT INTO sqlite_sequence VALUES('users',1);
CREATE UNIQUE INDEX "users_email_unique" on "users" ("email");
CREATE INDEX "sessions_user_id_index" on "sessions" ("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions" ("last_activity");
CREATE INDEX "jobs_queue_index" on "jobs" ("queue");
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs" ("uuid");
CREATE INDEX "medias_model_type_model_id_index" on "medias" ("model_type", "model_id");
COMMIT;
