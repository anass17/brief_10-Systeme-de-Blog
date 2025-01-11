create database blogs_db;

use blogs_db;

create table users (
	user_id int unsigned auto_increment primary key,
	first_name VARCHAR(50) not null,
	last_name VARCHAR(50) not null,
	email VARCHAR(50) not null unique,
	password VARCHAR(255) not null,
	role ENUM('visitor', 'user', 'admin', 'super_admin') not null,
	token VARCHAR(100),
	token_expiration datetime
)

alter table users add register_date TIMESTAMP default current_timestamp;

insert into users (first_name, last_name, email, password, role) values 
('Anass', 'Boutaib', 'anass@gmail.com', '$2y$12$hRFfFy8TKKEmKdoKUeMoyuaTE.WOVInspOjhg7tWIdnMMkSHiw2ZS', 'super_admin'),
('Ahmed', 'Taoudi', 'ahmed@gmail.com', '$2y$12$hRFfFy8TKKEmKdoKUeMoyuaTE.WOVInspOjhg7tWIdnMMkSHiw2ZS', 'admin'),
('User', 'Unknown', 'user@gmail.com', '$2y$12$hRFfFy8TKKEmKdoKUeMoyuaTE.WOVInspOjhg7tWIdnMMkSHiw2ZS', 'user');

alter table users add image_url VARCHAR(100) default '' after role;

create table posts (
	post_id int unsigned auto_increment primary key,
	title VARCHAR(150) not null,
	content text not null,
	post_image_url VARCHAR(100) default '',
	post_author int unsigned,
	foreign key (post_author) references users(user_id) on update cascade on delete cascade
)

alter table posts add publish_date TIMESTAMP default current_timestamp;

insert into posts (title, content, post_image_url, post_author) values 
('Web Development Trends in 2025', 'In this post, we’ll explore the emerging trends in web development, from advanced frameworks like React and Vue to the integration of AI in creating smarter web applications. We will also discuss the growing importance of web performance optimization and responsive design.', '', 3),
('Machine Learning Basics', 'This article will serve as an introductory guide to machine learning, explaining key concepts such as supervised vs unsupervised learning, neural networks, and training algorithms. Whether you are a newcomer or looking to refresh your knowledge, this guide will help you get started in ML.', '', 3),
('Mastering JavaScript', 'Learn the latest JavaScript features and best practices to stay ahead in web development. This post covers key updates and tools that can help you master JavaScript.', '', 2),
('Protect Your Website Against Attacks', 'This post covers essential cybersecurity tips for website owners. Learn how to secure your site from potential threats and protect user data with the latest security measures.', '', 1),
('Transforming Patient Care', 'Explore how AI is revolutionizing healthcare, from diagnosing diseases to personalizing patient care. This post delves into innovative AI applications improving medical outcomes.', '', 3);

create table comments (
	comment_id int unsigned auto_increment primary key,
	content text not null,
	publish_date timestamp default current_timestamp,
	comment_author int unsigned,
	comment_post int unsigned,
	foreign key (comment_author) references users (user_id) on delete cascade on update cascade,
	foreign key (comment_post) references posts (post_id) on delete cascade on update cascade
)

create table tags (
	tag_id int unsigned auto_increment primary key,
	tag_name varchar(50) not null
)

insert into tags (tag_name) values
("HTML"),
("CSS"),
("Design"),
("Front-End"),
("Back-End"),
("IT"),
("SEO"),
("JavaScript"),
("AI"),
("TypeScript"),
("Full-Stack"),
("Web development"),
("Machine Learning"),
("Programming"),
("Development"),
("GitHub"),
("Git"),
("OOP"),
("SQL"),
("PHP"),
("MySQL");

create table post_tags (
	tag_id int unsigned,
	post_id int unsigned,
	foreign key (tag_id) references tags(tag_id) on update cascade on delete cascade,
	foreign key (post_id) references posts(post_id) on update cascade on delete cascade
)

insert into post_tags (tag_id, post_id) values
(1, 1),
(2, 1),
(3, 1),
(7, 1),
(1, 2),
(5, 2),
(6, 2),
(10, 2),
(15, 3),
(19, 3);


create table reactions (
	user_id int unsigned,
	post_id int unsigned,
	type ENUM("Like", "Dislike") not null,
	primary key(user_id, post_id),
	foreign key (user_id) references users(user_id) on update cascade on delete cascade,
	foreign key (post_id) references posts(post_id) on update cascade on delete cascade
)
