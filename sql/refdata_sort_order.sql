
ALTER TABLE refdata add column sort_order int;

UPDATE refdata SET sort_order = 0 WHERE id = 136;
UPDATE refdata SET sort_order = 1 WHERE id = 309;
UPDATE refdata SET sort_order = 2 WHERE id = 310;
UPDATE refdata SET sort_order = 3 WHERE id = 137;
UPDATE refdata SET sort_order = 4 WHERE id = 138;
UPDATE refdata SET sort_order = 5 WHERE id = 139;
UPDATE refdata SET sort_order = 6 WHERE id = 140;
UPDATE refdata SET sort_order = 7 WHERE id = 141;
UPDATE refdata SET sort_order = 8 WHERE id = 142;
UPDATE refdata SET sort_order = 9 WHERE id = 143;
UPDATE refdata SET sort_order = 10 WHERE id = 144;
UPDATE refdata SET sort_order = 11 WHERE id = 145;
UPDATE refdata SET sort_order = 12 WHERE id = 146;
UPDATE refdata SET sort_order = 13 WHERE id = 147;

