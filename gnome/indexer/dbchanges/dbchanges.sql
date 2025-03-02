-- database changes 10-29-2022

ALTER TABLE `figu-az`.`indices` 
ADD COLUMN `created_by` VARCHAR(100) NULL AFTER `text_color`;


-- database change 11-10-2022

ALTER TABLE `figu-az`.`user` 
ADD COLUMN `userId` INT NULL FIRST;
ALTER TABLE `figu-az`.`user` 
MODIFY `userId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY;
ALTER TABLE `figu-az`.`user` 
CHANGE COLUMN `userId` `user_id` INT NOT NULL AUTO_INCREMENT ;

ALTER TABLE `figu-az`.`indices_permission` 
CHANGE COLUMN `username` `user_id` INT NOT NULL ,
DROP PRIMARY KEY;

-- database change 11-13-2022

ALTER TABLE `figu-az`.`indices_permission` 
ADD UNIQUE INDEX `indices_id_UNIQUE` (`indices_id` ASC) VISIBLE,
ADD UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC) VISIBLE;

ALTER TABLE `figu-az`.`indices_permission` 
ADD PRIMARY KEY (`indices_id`, `user_id`),
DROP INDEX `user_id_UNIQUE` ,
DROP INDEX `indices_id_UNIQUE` ;
;


-- database change 11-14-2022
ALTER TABLE `figu-az`.`indices` 
ADD COLUMN `created_by` VARCHAR(100) NULL AFTER `text_color`;


ALTER TABLE `figu-az`.`indices` 
ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`;

-- database change 01-05-2023

ALTER TABLE `figu-az`.`publications` 
DROP FOREIGN KEY `publication_type_id`;
ALTER TABLE `figu-az`.`publications` 
DROP INDEX `publication_type_id_idx` ;
;
ALTER TABLE `figu-az`.`publications` 
ADD CONSTRAINT `publication_type_id`
  FOREIGN KEY ()
  REFERENCES `figu-az`.`publication_type` ();

ALTER TABLE `figu-az`.`publications` 
DROP COLUMN `publication_type_id`;

ALTER TABLE `figu-az`.`publication_type` 
CHANGE COLUMN `publication_type_id` `publication_type_id` INT NULL ,
DROP PRIMARY KEY;
;

ALTER TABLE `figu-az`.`publication_type` 
CHANGE COLUMN `publication_type_id` `publication_type_id` INT NOT NULL AUTO_INCREMENT ;


INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('FIGU Ratgeber ( Guidebook )', 'RATG');
INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('FIGU Zeitzeichen ( Sign of the times )', 'ZEIT');
INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('FIGU Bulletin', 'BUL');
INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('FIGU Sonder-Bulletin ( Special Bulletin )', 'SONBUL');
INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('FIGU in bezug auf Überbevölke-rung ( in relation to over-population )', 'UBERBEV');
INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('FIGU Offene Worte der Wahrheit und Zeit ( Open Words of Truth and Time )', 'OFFWOR');
INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('Offener Brief ( Open Letter )', 'OFFBRF');
INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('Über andere richten ( Judgement About Others )', 'UBERRIC');
INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('The Psyche', 'PSY');
INSERT INTO `figu-az`.`publication_type` (`name`, `abbreviation`) VALUES ('Contact Conversation Report', 'CTR');


ALTER TABLE `figu-az`.`publications` 
ADD COLUMN `publication_type_id` INT NULL AFTER `is_ready`;
-- updated January 10 2023


-- January 21 2023
ALTER TABLE `figu-az`.`publications` 
CHANGE COLUMN `publication_type_id` `publication_type_id` INT NULL DEFAULT NULL AFTER `publication_id`;


-- February 6th
-- ALTER TABLE `figu-az`.`publications` 
-- ADD COLUMN `raw_html_summary` VARCHAR(4000) NULL DEFAULT NULL AFTER `raw_html`;


-- February 6th 2023
CREATE TABLE `publication_keyword_ignore` (
  `publication_keyword_ignore_id` int NOT NULL AUTO_INCREMENT,
  `keyword_ignore` varchar(240) NOT NULL,
  PRIMARY KEY (`publication_keyword_ignore_id`),
  UNIQUE KEY `keyword_ignore_UNIQUE` (`keyword_ignore`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `figu-az`.`publication_keyword_ignore`
(`keyword_ignore`)
VALUES
('a'),('able'),('about'),('above'),('abroad'),('according'),('accordingly'),('across'),('actually'),('adj'),('after'),('afterwards'),('again'),('against'),('ago'),('ahead'),('ain’t'),('all'),('allow'),('allows'),('almost'),('alone'),('along'),('alongside'),('already'),('also'),('although'),('always'),('am'),('amid'),('amidst'),('among'),('amongst'),('an'),('and'),('another'),('any'),('anybody'),('anyhow'),('anyone'),('anything'),('anyway'),('anyways'),('anywhere'),('apart'),('appear'),('appreciate'),('appropriate'),('are'),('aren’t'),('around'),('as'),('a’s'),('aside'),('ask'),('asking'),('associated'),('at'),('available'),('away'),('awfully'),('b'),('back'),('backward'),('backwards'),('be'),('became'),('because'),('become'),('becomes'),('becoming'),('been'),('before'),('beforehand'),('begin'),('behind'),('being'),('believe'),('below'),('beside'),('besides'),('best'),('better'),('between'),('beyond'),('both'),('brief'),('but'),('by'),('c'),('came'),('can'),('cannot'),('cant'),('can’t'),('caption'),('cause'),('causes'),('certain'),('certainly'),('changes'),('clearly'),('c’mon'),('co'),('co.,com'),('come'),('comes'),('concerning'),('consequently'),('consider'),('considering'),('contain'),('containing'),('contains'),('corresponding'),('could'),('couldn’t'),('course'),('c’s'),('currently'),('d'),('dare'),('daren’t'),('definitely'),('described'),('despite'),('did'),('didn’t'),('different'),('directly'),('do'),('does'),('doesn’t'),('doing'),('done'),('don’t'),('down'),('downwards'),('during'),('e'),('each'),('edu'),('eg'),('eight'),('eighty'),('either'),('else'),('elsewhere'),('end'),('ending'),('enough'),('entirely'),('especially'),('et'),('etc'),('even'),('ever'),('evermore'),('every'),('everybody'),('everyone'),('everything'),('everywhere'),('ex'),('exactly'),('example'),('except'),('f'),('fairly'),('far'),('farther'),('few'),('fewer'),('fifth'),('first'),('five'),('followed'),('following'),('follows'),('for'),('forever'),('former'),('formerly'),('forth'),('forward'),('found'),('four'),('from'),('further'),('furthermore'),('g'),('get'),('gets'),('getting'),('given'),('gives'),('go'),('goes'),('going'),('gone'),('got'),('gotten'),('greetings'),('h'),('had'),('hadn’t'),('half'),('happens'),('hardly'),('has'),('hasn’t'),('have'),('haven’t'),('having'),('he'),('he’d'),('he’ll'),('hello'),('help'),('hence'),('her'),('here'),('hereafter'),('hereby'),('herein'),('here’s'),('hereupon'),('hers'),('herself'),('he’s'),('hi'),('him'),('himself'),('his'),('hither'),('hopefully'),('how'),('howbeit'),('however'),('hundred'),('i'),('i’d'),('ie'),('if'),('ignored'),('i’ll'),('i’m'),('immediate'),('in'),('inasmuch'),('inc'),('inc.,indeed'),('indicate'),('indicated'),('indicates'),('inner'),('inside'),('insofar'),('instead'),('into'),('inward'),('is'),('isn’t'),('it'),('it’d'),('it’ll'),('its'),('it’s'),('itself'),('i’ve'),('j'),('just'),('k'),('keep'),('keeps'),('kept'),('know'),('known'),('knows'),('l'),('last'),('lately'),('later'),('latter'),('latterly'),('least'),('less'),('lest'),('let'),('let’s'),('like'),('liked'),('likely'),('likewise'),('little'),('look'),('looking'),('looks'),('low'),('lower'),('ltd'),('m'),('made'),('mainly'),('make'),('makes'),('many'),('may'),('maybe'),('mayn’t'),('me'),('mean'),('meantime'),('meanwhile'),('merely'),('might'),('mightn’t'),('mine'),('minus'),('miss'),('more'),('moreover'),('most'),('mostly'),('mr'),('mrs'),('much'),('must'),('mustn’t'),('my'),('myself'),('n'),('name'),('namely'),('nd'),('near'),('nearly'),('necessary'),('need'),('needn’t'),('needs'),('neither'),('never'),('neverf'),('neverless'),('nevertheless'),('new'),('next'),('nine'),('ninety'),('no'),('nobody'),('non'),('none'),('nonetheless'),('noone'),('no-one'),('nor'),('normally'),('not'),('nothing'),('notwithstanding'),('novel'),('now'),('nowhere'),('o'),('obviously'),('of'),('off'),('often'),('oh'),('ok'),('okay'),('old'),('on'),('once'),('one'),('ones'),('one’s'),('only'),('onto'),('opposite'),('or'),('other'),('others'),('otherwise'),('ought'),('oughtn’t'),('our'),('ours'),('ourselves'),('out'),('outside'),('over'),('overall'),('own'),('p'),('particular'),('particularly'),('past'),('per'),('perhaps'),('placed'),('please'),('plus'),('possible'),('presumably'),('probably'),('provided'),('provides'),('q'),('que'),('quite'),('qv'),('r'),('rather'),('rd'),('re'),('really'),('reasonably'),('recent'),('recently'),('regarding'),('regardless'),('regards'),('relatively'),('respectively'),('right'),('round'),('s'),('said'),('same'),('saw'),('say'),('saying'),('says'),('second'),('secondly'),('see'),('seeing'),('seem'),('seemed'),('seeming'),('seems'),('seen'),('self'),('selves'),('sensible'),('sent'),('serious'),('seriously'),('seven'),('several'),('shall'),('shan’t'),('she'),('she’d'),('she’ll'),('she’s'),('should'),('shouldn’t'),('since'),('six'),('so'),('some'),('somebody'),('someday'),('somehow'),('someone'),('something'),('sometime'),('sometimes'),('somewhat'),('somewhere'),('soon'),('sorry'),('specified'),('specify'),('specifying'),('still'),('sub'),('such'),('sup'),('sure'),('t'),('take'),('taken'),('taking'),('tell'),('tends'),('th'),('than'),('thank'),('thanks'),('thanx'),('that'),('that’ll'),('thats'),('that’s'),('that’ve'),('the'),('their'),('theirs'),('them'),('themselves'),('then'),('thence'),('there'),('thereafter'),('thereby'),('there’d'),('therefore'),('therein'),('there’ll'),('there’re'),('theres'),('there’s'),('thereupon'),('there’ve'),('these'),('they'),('they’d'),('they’ll'),('they’re'),('they’ve'),('thing'),('things'),('think'),('third'),('thirty'),('this'),('thorough'),('thoroughly'),('those'),('though'),('three'),('through'),('throughout'),('thru'),('thus'),('till'),('to'),('together'),('too'),('took'),('toward'),('towards'),('tried'),('tries'),('truly'),('try'),('trying'),('t’s'),('twice'),('two'),('u'),('un'),('under'),('underneath'),('undoing'),('unfortunately'),('unless'),('unlike'),('unlikely'),('until'),('unto'),('up'),('upon'),('upwards'),('us'),('use'),('used'),('useful'),('uses'),('using'),('usually'),('v'),('value'),('various'),('versus'),('very'),('via'),('viz'),('vs'),('w'),('want'),('wants'),('was'),('wasn’t'),('way'),('we'),('we’d'),('welcome'),('well'),('we’ll'),('went'),('were'),('we’re'),('weren’t'),('we’ve'),('what'),('whatever'),('what’ll'),('what’s'),('what’ve'),('when'),('whence'),('whenever'),('where'),('whereafter'),('whereas'),('whereby'),('wherein'),('where’s'),('whereupon'),('wherever'),('whether'),('which'),('whichever'),('while'),('whilst'),('whither'),('who'),('who’d'),('whoever'),('whole'),('who’ll'),('whom'),('whomever'),('who’s'),('whose'),('why'),('will'),('willing'),('wish'),('with'),('within'),('without'),('wonder'),('won’t'),('would'),('wouldn’t'),('y'),('yes'),('yet'),('you'),('you’d'),('you’ll'),('your'),('you’re'),('yours'),('yourself'),('yourselves'),('you’ve'),('z'),('zero');

-- February 19th 2023
ALTER TABLE `figu-az`.`indices_permission` 
ADD COLUMN `can_admin` TINYINT NULL DEFAULT '0' AFTER `can_write`;


-- February 26th 2023 
DROP TABLE `figu-az`.`publication_keyword_link`;


-- December 10 2023
CREATE TABLE `figu-az`.`indices_link` (
  `indices_group_id` INT NOT NULL,
  `indices_id` INT NOT NULL,
  PRIMARY KEY (`indices_group_id`, `indices_id`));

-- December 17 2023
ALTER TABLE `figu-az`.`publication_type` 
ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC) VISIBLE,
ADD UNIQUE INDEX `abbreviation_UNIQUE` (`abbreviation` ASC) VISIBLE;
;

-- January 28 2024
CREATE TABLE `figu-az`.`publication_index_cache` (
  `publication_id` VARCHAR(100) NOT NULL,
  `indices_id` INT NOT NULL,
  `track_value_json` JSON NULL,
  PRIMARY KEY (`publication_id`, `indices_id`));

-- March 4 2024
CREATE TABLE `indicies_master_list_keyword_link` (
  `publication_keyword_id` int NOT NULL,
  `indices_id` int NOT NULL,
  UNIQUE KEY `keyword_glob_UNIQUE` (`indices_id`),
  UNIQUE KEY `indicies_global_keyword_id_UNIQUE` (`publication_keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='links the keyword to the indicies';

CREATE TABLE `indicies_master_list_keyword_link` (
  `publication_keyword_id` int NOT NULL,
  `indices_id` int NOT NULL,
  UNIQUE KEY `keyword_glob_UNIQUE` (`indices_id`),
  UNIQUE KEY `indicies_global_keyword_id_UNIQUE` (`publication_keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='links the keyword to the indicies';

-- April 13th
CREATE TABLE `indices_master_list_keyword_link` (
  `indices_id` int NOT NULL,
  `publication_keyword_id` int NOT NULL,
  `publication_id` varchar(20) NOT NULL,
  `search_complete` tinyint NOT NULL DEFAULT '0',
  `is_word_found` tinyint NOT NULL DEFAULT '0',
  UNIQUE KEY `indicies_id_UNIQUE` (`indices_id`),
  UNIQUE KEY `publication_keyword_id_UNIQUE` (`publication_keyword_id`),
  UNIQUE KEY `publication_id_UNIQUE` (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='This table is for keeping reference to whether the publication was searched with the current keyword';


-- May 6th
CREATE TABLE `indicies_master_keyword_publication_status` (
  `indices_id` int NOT NULL,
  `publication_keyword_id` int NOT NULL,
  `publication_id` varchar(20) NOT NULL,
  `search_complete` tinyint NOT NULL DEFAULT '0',
  `is_word_found` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`publication_id`,`publication_keyword_id`,`indices_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='This table is for keeping reference to whether the publication was searched with the current keyword';


CREATE TABLE `publication_text_content` (
  `publication_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `english_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `german_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`publication_id`),
  FULLTEXT KEY `ft_index` (`english_text`,`german_text`),
  CONSTRAINT `publication_text_content_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


ALTER TABLE publication_text_content CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
