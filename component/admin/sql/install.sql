DROP TABLE IF EXISTS `#__wimtvpro_playlist`;

CREATE TABLE IF NOT EXISTS `#__wimtvpro_playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id of Playlist',
  `name` varchar(100) DEFAULT NULL COMMENT 'Name of Playlist',
  `listVideo` text COMMENT 'List video contentidentifier',
  `uid` varchar(100) NOT NULL COMMENT 'User identifier',
  `option` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mycolumn1` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Playlist Api WIMTV' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `#__wimtvpro_videos`;

CREATE TABLE IF NOT EXISTS `#__wimtvpro_videos` (
  `uid` varchar(100) NOT NULL COMMENT 'User identifier',
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifier Video',
  `contentidentifier` varchar(100) NOT NULL COMMENT 'Contentidentifier Video',
  `state` varchar(100) NOT NULL COMMENT 'Showtime or no',
  `status` varchar(100) NOT NULL COMMENT 'OWNED-ACQUIRED-PERFORMING',
  `acquiredIdentifier` varchar(100) NOT NULL,
  `mytimestamp` int(11) NOT NULL COMMENT 'My timestamp',
  `position` int(11) NOT NULL COMMENT 'Position video user',
  `viewVideoModule` int(11) NOT NULL COMMENT 'View video into page or block',
  `urlThumbs` text NOT NULL COMMENT 'Url thumbs video',
  `urlPlay` text COMMENT 'Url preview video',
  `category` text NOT NULL COMMENT 'Category and subcategory video[Json]',
  `title` varchar(100) NOT NULL COMMENT 'Title videos',
  `duration` varchar(10) NOT NULL COMMENT 'Duration videos',
  `showtimeIdentifier` varchar(100) NOT NULL COMMENT 'showtimeIdentifier videos',
  `channel` int(11) DEFAULT NULL COMMENT 'Tid of taxonomy channel',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mycolumn1` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Video Api WIMTV' AUTO_INCREMENT=203 ;