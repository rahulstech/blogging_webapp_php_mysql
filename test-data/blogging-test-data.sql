INSERT INTO `users` (`userId`,`username`,`passwordHash`,`firstName`,`lastName`,`email`,`joinedOn`) VALUES 
(1,"testuser1","$2y$10$cF2PxqnG00nr98OEiSNEre.ARkQAxDFq/n13pjzs4vr37rCEQzvvi","FirstName1","LastName1","email1@domain.com",DATETIME("2021-04-10 13:21:56")),
(2,"testuser2","$2y$10$FKL2gPVrAHPVf.X3nW9ts.Rp5HJRIZAVofuzxpwZE6QhOaNhVWnQu","FirstName2","LastName2","email2@domain.com",DATETIME("2021-12-10 14:26:51")),
(3,"testuser3","$2y$10$HSEmEiEmLhBKwvwKDa0QxundthbZygxALtambFJ/kOs79/AQWX/A6","FirstName3","LastName3","email3@domain.com",DATETIME("2021-01-19 12:05:00")),
(4,"iamuser4","$2y$10$X7CZ5M8o.LEYfoGr9QIJtuLEthgfheU4AcE00qv/zZyOCOnnzxH1G","FirstName4","LastName4","email4@domain.com",DATETIME("2021-05-13 08:56:32"));

INSERT INTO `posts` (`creator_id`,`postId`,`title`,`shortDescription`,`createdOn`,`textContent`) VALUES
(1,1,"post 1 by testuser1","this is the first post of testuser1",DATETIME("2022-10-04 08:08:00"),
"this is the content of the first post by testuser1\nthis is the content of the first post by testuser1\nthis is the content of the first post by testuser1\nthis is the content of the first post by testuser1\nthis is the content of the first post by testuser1\nthis is the content of the first post by testuser1\n"),
(1,2,"post 2 by testuser1","this is the second post of testuser1",DATETiME("2022-10-04 13:30:00"),
"this is the content of the second post by testuser1\nthis is the content of the second post by testuser1\nthis is the content of the second post by testuser1\nthis is the content of the second post by testuser1\nthis is the content of the second post by testuser1\nthis is the content of the second post by testuser1\n"),
(1,6,"post new 2 by testuser1","this is the updated second post of testuser1",DATETiME("2022-10-03 19:00:00"),
"this is the content of the updated second post by testuser1\nthis is the content of the updated second post by testuser1\nthis is the content of the updated second post by testuser1\nthis is the content of the updated second post by testuser1\nthis is the content of the updated second post by testuser1\nthis is the content of the updated second post by testuser1\n"),


(2,3,"post 1 by testuser2","this is the first post of testuser2",DATETIME("2022-09-09 00:00:00"),
"this is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\n"),
(2,4,"post 2 by testuser2","this is the second post of testuser2",DATETIME("2022-01-06 00:00:00"),
"this is the content of the second post by testuser2\nthis is the content of the second post by testuserthis is the content of the second post by testuserthis is the content of the second post by testuserthis is the content of the second post by testuserthis is the content of the second post by testuserthis is the content of the second post by testuser"),

(3,5,"post 1 by testuser3","this is the first post of testuser3",DATETIME("2022-04-05 00:00:00"),
"this is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\n")
;
