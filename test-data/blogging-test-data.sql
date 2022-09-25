INSERT INTO `users` (`userId`,`username`,`firstName`,`lastName`,`email`) VALUES 
(1,"testuser1","FirstName1","LastName1","email1@domain.com"),
(2,"testuser2","FirstName2","LastName2","email2@domain.com"),
(3,"testuser3","FirstName3","LastName3","email3@domain.com"),
(4,"iamuser4","FirstName4","LastName4","email4@domain.com");

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
