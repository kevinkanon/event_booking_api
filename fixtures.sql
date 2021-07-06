INSERT INTO
    subscriber (id, "name", first_name, phone_number, email)
VALUES (1, 'Michel', 'Micou', '0658789645', 'micou@yahoo.fr'),
    (2, 'Salazard', 'serpentard', '0744789645', 'serpentard@yahoo.fr'),
    (3, 'bakari', 'bakalaye', '0689489645', 'baki@yahoo.fr'),
    (4, 'hanah', 'mobo', '0644789645', 'mobo@yahoo.fr'),
    (5, 'furious', 'fast', '0644789645', 'fast@yahoo.fr'),
    (7, 'negative', 'man', '0644789645', 'man@yahoo.fr'),
    (8, 'nice', 'techno', '0644789645', 'techno@yahoo.fr'),
    (9, 'PHP', 'Expert', '0644789645', 'php@yahoo.fr'),
    (10, 'Okay', 'positive', '0644789645', 'positive@yahoo.fr'),
    (11, 'Zlatan', 'Ibra', '0644789645', 'ibra@yahoo.fr');

INSERT INTO
    "event" (id, start_date, end_date)
VALUES (1, '2021-02-01 10:00:00.00', '2021-02-01 17:00:00.00'),
    (2, '2021-08-16 05:00:00.00', '2021-08-16 15:00:00.00'),
    (3, '2020-01-06 14:00:00.00', '2020-01-06 18:00:00.00'),
    (4, '2021-05-03 09:00:00.00', '2021-05-03 12:00:00.00'),
    (5, '2019-04-02 13:00:00.00', '2019-04-02 17:00:00.00');

INSERT INTO
    event_subscriber (id, event_id, subscriber_id)
VALUES(1, 1, 1),
    (2, 1, 2),
    (3, 1, 3),
    (4, 1, 4),
    (5, 1, 5),
    (6, 2, 6),
    (7, 3, 7),
    (8, 2, 8),
    (9, 4, 9),
    (10, 4, 10);

