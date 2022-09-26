INSERT INTO `users` (`userId`,`username`,`passwordHash`,`firstName`,`lastName`,`email`) VALUES 
(1,"testuser1","$2y$10$cF2PxqnG00nr98OEiSNEre.ARkQAxDFq/n13pjzs4vr37rCEQzvvi","FirstName1","LastName1","email1@domain.com"),
(2,"testuser2","$2y$10$FKL2gPVrAHPVf.X3nW9ts.Rp5HJRIZAVofuzxpwZE6QhOaNhVWnQu","FirstName2","LastName2","email2@domain.com"),
(3,"testuser3","$2y$10$HSEmEiEmLhBKwvwKDa0QxundthbZygxALtambFJ/kOs79/AQWX/A6","FirstName3","LastName3","email3@domain.com"),
(4,"iamuser4","$2y$10$X7CZ5M8o.LEYfoGr9QIJtuLEthgfheU4AcE00qv/zZyOCOnnzxH1G","FirstName4","LastName4","email4@domain.com");

INSERT INTO `posts` (`creator_id`,`postId`,`title`,`shortDescription`,`createdOn`,`textContent`) VALUES
(1,1,"post 1 by testuser1","this is the first post of testuser1",DATETiME("2022-10-04 08:08:00"),
"this is the content of the first post by testuser1\nthis is the content of the first post by testuser1\nthis is the content of the first post by testuser1\nthis is the content of the first post by testuser1\nthis is the content of the first post by testuser1\nthis is the content of the first post by testuser1\n"),
(1,2,"post 2 by testuser1","this is the second post of testuser1",DATETiME("2022-10-04 13:30:00"),
"this is the content of the second post by testuser1\nthis is the content of the second post by testuser1\nthis is the content of the second post by testuser1\nthis is the content of the second post by testuser1\nthis is the content of the second post by testuser1\nthis is the content of the second post by testuser1\n"),

(2,3,"post 1 by testuser2","this is the first post of testuser2",DATETiME("2022-09-09 00:00:00"),
"this is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\nthis is the content of the first post by testuser2\n"),
(2,4,"post 2 by testuser2","this is the second post of testuser2",DATETiME("2022-01-06 00:00:00"),
"this is the content of the second post by testuser2\nthis is the content of the second post by testuserthis is the content of the second post by testuserthis is the content of the second post by testuserthis is the content of the second post by testuserthis is the content of the second post by testuserthis is the content of the second post by testuser"),

(3,5,"post 1 by testuser3","this is the first post of testuser3",DATETiME("2022-04-05 00:00:00"),
"this is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\nthis is the content of the first post by testuser3\n")
;
