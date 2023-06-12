create table public.product_category (
	id serial primary key,
	category_name varchar(50) not null,
	created_at timestamp null,
	updated_at timestamp null,
	deleted_at timestamp null
);

create table public.product (
	id serial primary key,
	product_name varchar(50) not null,
	category_id integer not null,
	quantity integer not null,
	sold integer not null,
	available integer not null,
	created_at timestamp null,
	updated_at timestamp null,
	deleted_at timestamp null
);

alter table public.product add constraint product_category_id_fk 
foreign key (category_id) references public.product_category (id) 
on delete restrict on update cascade;

create table public.transaction (
	id serial primary key,
	product_name varchar(50) not null,
	category_name varchar(50) not null,
	quantity integer not null,
	transaction_date timestamp not null
);

insert into public.product_category (id, category_name, created_at, updated_at) 
values
	(1, 'Konsumsi', now(), now()),
	(2, 'Pembersih', now(), now());

select setval('product_category_id_seq', 2);

insert into public.product (id, product_name, category_id, quantity, sold, available, created_at, updated_at)
values
	(1, 'Kopi', 1, 100, 0, 100, now(), now()),
	(2, 'Teh', 1, 100, 0, 100, now(), now()),
	(3, 'Pasta Gigi', 2, 100, 0, 100, now(), now()),
	(4, 'Sabun Mandi', 2, 100, 0, 100, now(), now()),
	(5, 'Sampo', 2, 100, 0, 100, now(), now());

select setval('product_id_seq', 5);

insert into public.transaction (product_name, category_name, quantity, transaction_date)
values ('Kopi', 'Konsumsi', 10, '2021-05-01'::timestamp);
update public.product set available = 100 - 10, sold = 10 where id = 1;

insert into public.transaction (product_name, category_name, quantity, transaction_date) 
values ('Teh', 'Konsumsi', 19, '2021-05-05'::timestamp);
update public.product set available = 100 - 19, sold = 19 where id = 2;

insert into public.transaction (product_name, category_name, quantity, transaction_date) 
values ('Kopi', 'Konsumsi', 15, '2021-05-10'::timestamp);
update public.product set available = 90 - 15, sold = 10 + 15 where id = 1;

insert into public.transaction (product_name, category_name, quantity, transaction_date) 
values ('Pasta Gigi', 'Pembersih', 20, '2021-05-11'::timestamp);
update public.product set available = 100 - 20, sold = 20 where id = 3;

insert into public.transaction (product_name, category_name, quantity, transaction_date) 
values ('Sabun Mandi', 'Pembersih', 30, '2021-05-11'::timestamp);
update public.product set available = 100 - 30, sold = 30 where id = 4;

insert into public.transaction (product_name, category_name, quantity, transaction_date) 
values ('Sampo', 'Pembersih', 25, '2021-05-12'::timestamp);
update public.product set available = 100 - 25, sold = 25 where id = 5;

insert into public.transaction (product_name, category_name, quantity, transaction_date) 
values ('Teh', 'Konsumsi', 5, '2021-05-12'::timestamp);
update public.product set available = 81 - 5, sold = 19 + 5 where id = 2;
