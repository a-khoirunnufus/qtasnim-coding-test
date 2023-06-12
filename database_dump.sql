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
	product_id integer not null,
	quantity integer not null,
	created_at timestamp null,
	updated_at timestamp null,
	deleted_at timestamp null
);

alter table public.transaction add constraint transaction_product_id
foreign key (product_id) references public.product (id)
on delete restrict on update cascade;

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

insert into public.transaction (product_id, quantity, created_at)
values (1, 10, '2021-05-01'::timestamp);
update public.product set available = 100 - 10 where id = 1;

insert into public.transaction (product_id, quantity, created_at)
values (2, 19, '2021-05-05'::timestamp);
update public.product set available = 100 - 19 where id = 2;

insert into public.transaction (product_id, quantity, created_at)
values (1, 15, '2021-05-10'::timestamp);
update public.product set available = 90 - 15 where id = 1;

insert into public.transaction (product_id, quantity, created_at)
values (3, 20, '2021-05-11'::timestamp);
update public.product set available = 100 - 20 where id = 3;

insert into public.transaction (product_id, quantity, created_at)
values (4, 30, '2021-05-11'::timestamp);
update public.product set available = 100 - 30 where id = 4;

insert into public.transaction (product_id, quantity, created_at)
values (5, 25, '2021-05-12'::timestamp);
update public.product set available = 100 - 25 where id = 5;

insert into public.transaction (product_id, quantity, created_at)
values (2, 5, '2021-05-12'::timestamp);
update public.product set available = 81 - 5 where id = 2;
