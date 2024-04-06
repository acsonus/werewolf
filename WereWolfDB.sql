drop table if exists `user`;

create table `user` (
    id int not null auto_increment,
    name varchar(255),
    email varchar(255),
    nickname varchar(255),
    status varchar(50),
    -- active, inactive
    role varchar(50),
    --  moderator, civilian, werevolf, vampire
    main_vampire boolean,
    main_werevolf boolean,
    password varchar(255),
    primary key(id)
);

drop table if exists game;

create table game (
    id int not null auto_increment,
    name varchar(255),
    password varchar(255),
    description text,
    primary key(id)
);

drop table if exists user_game;

create table user_game (
    user_id int,
    game_id int,
    moderator bit,
    primary key(user_id, game_id)
);

drop table if exists game_round;

create table game_round(
    id int not null auto_increment,
    game_id int,
    round_number int,
    primary key(id)
);

drop table if exists game_round_user;

create table game_round_user(
    game_round_id int,
    user_id int,
    score int,
    primary key(game_round_id, user_id)
);

drop table if exists game_round_protected;

create table game_round_protected(
    game_round_protected_id int,
    user_id int,
    protected_by_werevolf boolean,
    protected_by_vampire boolean,
    primary key(game_round_protected_id, user_id)
);

drop table if exists game_round_bitten;

create table game_round_bitten(
    game_round_bitten_id int,
    user_id int,
    bitten_by_werevolf boolean,
    bitten_by_vampire boolean,
    primary key(game_round_bitten_id, user_id)
);

drop table if exists game_round_killed;

create table game_round_killed(
    game_round_killed_id int,
    user_id int,
    killed_by_werevolf boolean,
    killed_by_vampire boolean,
    primary key(game_round_killed_id, user_id)
);

drop table if exists game_round_winner;

create table game_round_winner(
    game_round_winner_id int,
    user_id int,
    primary key(game_round_winner_id, user_id)
);