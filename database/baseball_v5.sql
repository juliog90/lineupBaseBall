create table persons(
perId int primary key AUTO_INCREMENT not null,
perFirstName varchar(25) not null,
perLastName varchar(25) not null
);
alter table persons AUTO_INCREMENT=1;

create table players(
plaId int primary key AUTO_INCREMENT not null,
perId int not null, --  person id
staId tinyint not null, -- status id
teaId int, --  team id, puede no pertenecer a un equipo
plaNickname varchar(35),
plaBirthdate date not null,
plaDebut date, -- puede no haber debutado aun
plaImage varchar(50), -- puede no tener imagen supongo
plaNumber tinyint not null -- pos si no debuta no tiene numero ???, o puede que no este jugando
);
alter table  players AUTO_INCREMENT=1;

create table users(
usrId int primary key AUTO_INCREMENT not null,
perId int not null, --  person id
ulvId int not null, -- user level id
usrEmail varchar(50) not null,
usrPassword varchar(25) not null
);
alter table users AUTO_INCREMENT=1;

create table userPlayer(
plaId int not null,
usrId int not null
);
alter table userPlayer AUTO_INCREMENT=1;

create table userLevels(
ulvId int primary key AUTO_INCREMENT not null,
ulvDescription varchar(25) not null
);
alter table userLevels AUTO_INCREMENT=1;

create table coaches(
coaId int primary key AUTO_INCREMENT not null,
perId int not null -- person id
);
alter table coaches AUTO_INCREMENT=1;

create table teams(
teaId int primary key AUTO_INCREMENT not null,
staId tinyint not null,
teaName varchar(25) not null,
catId int not null, -- category id
coaId int not null -- coach id
);
alter table teams AUTO_INCREMENT=1;

create table status(
staId tinyint primary key not null,
staStatus varchar(25) not null
);
alter table status AUTO_INCREMENT=1;
-- ----------------------A LA HORA DE HACER EL LINE-UP ES NECESARIO HACER EL FILTRO POR 'STATUS' = 'ACTIVE'

create table playerStats(
pbsId int primary key AUTO_INCREMENT not null,
plaId int not null,
pbsPlayedGames smallint not null, -- PG
pbsWins smallint not null, -- W , G (W+L)
pbsLoses smallint not null, -- L, G
pbsHits smallint not null default 0, -- bat H
pbsHomeRuns smallint not null default 0, -- bat HR
pbsStrikes smallint not null default 0, -- bat st
pbsRuns smallint not null default 0, -- R
pbsBalls smallint not null default 0,	-- B
pbsOuts smallint not null default 0, -- O
pbsStolenBases smallint not null default 0 -- SB
);
alter table playerStats AUTO_INCREMENT=1;

create table teamStats(
tstId int primary key AUTO_INCREMENT not null,
teaId int not null,
tstWins smallint not null default 0,
tstLoses smallint not null default 0
);
alter table teamStats AUTO_INCREMENT=1;

create table categories( --  o division idk
catId int primary key AUTO_INCREMENT not null,
catName varchar(25) not null
);
alter table categories AUTO_INCREMENT=1;

create table seasons(
seaId int primary key AUTO_INCREMENT not null,
seaName varchar(25) not null
);
alter table seasons AUTO_INCREMENT=1;

create table matches(
matId int primary key AUTO_INCREMENT not null,
seaId int not null, --  season id
matHomeTeam int not null, -- team id
matGuestTeam int not null, -- team id
matField varchar(35) not null,
matStartTime datetime not null,
matEndTime datetime not null,
matRunsHomeTeam tinyint not null default 0,
matRunsGuestTeam tinyint not null default 0
--  winner deberia ser calculado no??
);
alter table matches AUTO_INCREMENT=1;

create table lineups(
lupId int primary key AUTO_INCREMENT not null,
plaId int not null, -- player id
teaId int not null, -- team id
lupBattingTurn tinyint not null,
posId char(2) not null, -- position id
matId int not null
-- se borrara al final de cada partido
);
alter table lineups AUTO_INCREMENT=1;

create table positions(
posId char(2) primary key not null,
posName varchar(25) not null
);
alter table positions AUTO_INCREMENT=1;

create table displayMatches(
matId int primary key not null, -- match id serviria de primaria tambien i guess
dmtEntry tinyint not null default 0,
dmtBatter int not null default 0, -- player id
dmtBalls tinyint not null default 0,
dmtStrikes tinyint not null default 0,
dmtOuts tinyint not null default 0,
dmtRunsHomeTeam tinyint not null default 0,
dmtRunsGuestTeam tinyint not null default 0,
dmtBase1 tinyint not null default 0, -- player id
dmtBase2 tinyint not null default 0, -- player id
dmtBase3 tinyint not null default 0 -- player id
);
alter table displayMatches AUTO_INCREMENT=1;

-- PENDIENTE de terminar (creo)
create table matchesHistory(
mthId int primary key AUTO_INCREMENT not null,
matId int not null, -- match id
plaId int not null, -- player id
-- teaId int not null, -- team id para filtro
actId char(4) not null, -- action id
mthDate datetime not null default CURRENT_TIMESTAMP, --  pues cuando
mthEntry tinyint not null -- en que entrada (servira de filtro)
);
alter table matchesHistory AUTO_INCREMENT=1;

create table actions(
actId char(4) primary key not null,
actDescription varchar(25) not null --  ball, strike, run, out,etc y donde paso en caso de ser necesario
);
alter table actions AUTO_INCREMENT=1;

/*
create table (
Id int primary key AUTO_INCREMENT not null,
);
alter table  AUTO_INCREMENT=1;
*/

-- --------------------------------------------- llaves

-- players
alter table players
add constraint FK_Person_Player foreign key (perId) references persons(perId);

alter table players
add constraint FK_Team_Player foreign key (teaId) references teams(teaId);

alter table players
add constraint FK_Status_Player foreign key (staId) references status(staId);


-- player stats
alter table playerStats
add constraint FK_Player_Stats foreign key (plaId) references players(plaId);

-- users
alter table users
add constraint FK_Person_User foreign key (perId) references persons(perId);

alter table users
add constraint FK_UserLevel_User foreign key (ulvId) references userLevels(ulvId);

-- user - player (en caso de ser un padre)

alter table userPlayer
add primary key(plaId, usrId);

alter table userPlayer
add constraint FK_User_UserPlayer foreign key (usrId) references users(usrId);

alter table userPlayer
add constraint FK_Player_UserPlayer foreign key (plaId) references players(plaId);

-- coaches
alter table coaches
add constraint FK_Person_Coach foreign key (perId) references persons(perId);

-- teams
alter table teams
add constraint FK_Coach_Team foreign key (coaId) references coaches(coaId);

alter table teams
add constraint FK_Category_Team foreign key (catId) references categories(catId);

alter table teams
add constraint FK_Status_Team foreign key (staId) references status(staId);

-- teams stats

alter table teamStats
add constraint FK_Team_Stats foreign key (teaId) references teams(teaId);

-- lineups
alter table lineups
add constraint FK_Player_Lineup foreign key (plaId) references players(plaId);

alter table lineups
add constraint FK_Position_Lineup foreign key (posId) references positions(posId);

alter table lineups
add constraint FK_Match_Lineup foreign key (matId) references matches(matId);

-- matches

alter table matches
add constraint FK_Season_Match foreign key (seaId) references seasons(seaId);

alter table matches
add constraint FK_HomeTeam_Match foreign key (matHomeTeam) references teams(teaId);

alter table matches
add constraint FK_GuestTeam_Match foreign key (matGuestTeam) references teams(teaId);


-- matches history

alter table matchesHistory
add constraint FK_Match_MatchHistory foreign key (matId) references matches(matId);

alter table matchesHistory
add constraint FK_Player_MatchHistory foreign key (plaId) references players(plaId);

alter table matchesHistory
add constraint FK_Action_MatchHistory foreign key (actId) references actions(actId);


-- displayMatches

alter table displayMatches
add constraint FK_Match_DisplayMatch foreign key (matId) references matches(matId);


-- -------------------------- views

create view MatchTeamResults
as
select m.matId,
case
when m.matRunsGuestTeam < matRunsHomeTeam then matHomeTeam
when matRunsGuestTeam > matRunsHomeTeam then matGuestTeam
else null
end W,
case
when m.matRunsGuestTeam > matRunsHomeTeam then matHomeTeam
when matRunsGuestTeam < matRunsHomeTeam then matGuestTeam
else null
end L,
case
when m.matRunsGuestTeam = matRunsHomeTeam then 1
else 0
end tie
from matches m;

-- scraped
/*
create view playerStats
as
select p.plaId id,
bs.pbsPlayedGames + count(mr.W) + count(mr.L) + count(tie) PG, -- played games
bs.pbsWins + count(mr.W) W,
bs.pbsLoses + count(mr.L) L,
bs.pbsHits + bs.pbsStrikes + (select count(*) from matcheshistory mh where mh.actId = 'H' or mh.actId = 'st' and mh.plaId = bs.plaId) bats,
bs.pbsHits + (select count(*) from matcheshistory mh where mh.actId = 'H' and mh.plaId = bs.plaId) H,
bs.pbsHits + bs.pbsHomeRuns + (select count(*) from matcheshistory mh where mh.actId = 'H' or mh.actId = 'HR' and mh.plaId = bs.plaId) HR,
bs.pbsStrikes + (select count(*) from matcheshistory mh where mh.actId = 'st' and mh.plaId = bs.plaId) strikes,
bs.pbsRuns + (select count(*) from matcheshistory mh where mh.actId = 'R' and mh.plaId = bs.plaId) R,
bs.pbsBalls + (select count(*) from matcheshistory mh where mh.actId = 'B' and mh.plaId = bs.plaId) B,
bs.pbsOuts + (select count(*) from matcheshistory mh where mh.actId = 'GO' or mh.actId = 'FO' and mh.plaId = bs.plaId) O,
bs.pbsStolenBases + (select count(*) from matcheshistory mh where mh.actId = 'SB' and mh.plaId = bs.plaId) SB
from players p
join playerStats bs on p.plaId = bs.plaId
join matchesHistory mh on p.plaId = mh.plaId
join MatchTeamResults mr on mr.matId = mh.matId
where mr.tie is not 0
*/

-- --------------------- static inserts

-- actions
insert into actions (actId, actDescription) VALUES
('H', 'Hit'),
('HR', 'Home Run'),
('st', 'Strike'),
('R', 'Run'),
('B', 'Ball'),
('FO', 'Fly Out'),
('GO', 'Ground Out'),
('SB', 'Stolen Base');

-- status
insert into status (staId, staStatus) VALUES
(1, 'Active'),
(2, 'Inactive'),
(3, 'Wounded');


-- positions

insert into positions (posId, posName) VALUES
('P', 'Pitcher'),
('C', 'Catcher'),
('1B', 'First Base'),
('2B', 'Second Base'),
('3B', 'Third Base'),
('SS', 'Short Stop'),
('CF', 'Center Field'),
('LF', 'Left Field'),
('RF', 'RightField');

-- user levels

insert into userLevels (ulvId, ulvDescription) VALUES
(1, 'Viewer'),
(2, 'Tutor'),
(3, 'Coach'),
(4, 'Scorekeeper'),
(5, 'Admin');