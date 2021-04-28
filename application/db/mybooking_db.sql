SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

CREATE TABLE IF NOT EXISTS booking (
  booking_id int(11) NOT NULL AUTO_INCREMENT,
  fk_user_id int(11) NOT NULL DEFAULT '0',
  booking_ref_no varchar(16) NOT NULL DEFAULT '',
  booking_type int(1) NOT NULL DEFAULT '0',
  full_day tinyint(1) NOT NULL DEFAULT '0',
  fk_booking_id int(11) NOT NULL DEFAULT '0',
  start_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  end_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  booking_status tinyint(2) NOT NULL DEFAULT '0',
  booking_mode tinyint(1) NOT NULL DEFAULT '0',
  booking_closed tinyint(1) NOT NULL DEFAULT '0',
  booking_status_description varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (booking_id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'booking_equipments'
--

CREATE TABLE IF NOT EXISTS booking_equipments (
  booking_equipment_id int(11) NOT NULL AUTO_INCREMENT,
  secretariat varchar(64) COLLATE utf8_bin NOT NULL,
  booking_with_room tinyint(1) NOT NULL,
  equipment_purpose varchar(150) COLLATE utf8_bin NOT NULL DEFAULT '',
  place varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  require_technical_person int(4) NOT NULL DEFAULT '0',
  booking_equipment_description text COLLATE utf8_bin NOT NULL,
  equipment_list varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (booking_equipment_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'booking_foods'
--

CREATE TABLE IF NOT EXISTS booking_foods (
  booking_food_id int(11) NOT NULL AUTO_INCREMENT,
  secretariat int(11) NOT NULL,
  food_type_ids varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  place varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  food_purpose varchar(150) COLLATE utf8_bin NOT NULL DEFAULT '',
  total_pack int(2) NOT NULL DEFAULT '0',
  booking_food_description text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (booking_food_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'booking_rooms'
--

CREATE TABLE IF NOT EXISTS booking_rooms (
  booking_room_id int(11) NOT NULL AUTO_INCREMENT,
  secretariat varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  chairman varchar(64) COLLATE utf8_bin NOT NULL,
  room_purpose varchar(150) COLLATE utf8_bin NOT NULL DEFAULT '',
  total_from_agensi int(4) NOT NULL DEFAULT '0',
  total_from_nonagensi int(4) NOT NULL DEFAULT '0',
  description text COLLATE utf8_bin NOT NULL,
  fk_room_id int(4) NOT NULL DEFAULT '0',
  fk_booking_equipment_id int(4) NOT NULL DEFAULT '0',
  fk_booking_food_id int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (booking_room_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'booking_transports'
--

CREATE TABLE IF NOT EXISTS booking_transports (
  booking_transport_id int(11) NOT NULL AUTO_INCREMENT,
  secretariat varchar(64) COLLATE utf8_bin NOT NULL,
  destination_from varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  destination_to varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  transport_purpose varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  total_passenger int(2) NOT NULL DEFAULT '0',
  trip_type int(1) NOT NULL DEFAULT '0',
  transports varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  booking_transport_description varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (booking_transport_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'ci_sessions'
--

CREATE TABLE IF NOT EXISTS ci_sessions (
  session_id varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  ip_address varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  user_agent varchar(150) COLLATE utf8_bin NOT NULL,
  last_activity int(10) unsigned NOT NULL DEFAULT '0',
  user_data text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'destinations'
--

CREATE TABLE IF NOT EXISTS destinations (
  destination_name varchar(128) DEFAULT NULL,
  UNIQUE KEY destination_name (destination_name)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'email_queue'
--

CREATE TABLE IF NOT EXISTS email_queue (
  id int(11) NOT NULL AUTO_INCREMENT,
  to_email varchar(128) NOT NULL,
  `subject` varchar(255) NOT NULL,
  message text NOT NULL,
  alt_message text,
  max_attempts int(11) NOT NULL DEFAULT '3',
  attempts int(11) NOT NULL DEFAULT '0',
  success tinyint(1) NOT NULL DEFAULT '0',
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_attempt datetime DEFAULT NULL,
  date_sent datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'equipments'
--

CREATE TABLE IF NOT EXISTS equipments (
  equipment_id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  description varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  quantity int(5) NOT NULL DEFAULT '0',
  deleted tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  status_description varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (equipment_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'login_attempts'
--

CREATE TABLE IF NOT EXISTS login_attempts (
  id int(11) NOT NULL AUTO_INCREMENT,
  ip_address varchar(40) COLLATE utf8_bin NOT NULL,
  login varchar(50) COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'logs'
--

CREATE TABLE IF NOT EXISTS `logs` (
  id int(11) NOT NULL AUTO_INCREMENT,
  fk_user_id int(11) NOT NULL DEFAULT '0',
  activity varchar(255) NOT NULL DEFAULT '',
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted tinyint(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'rooms'
--

CREATE TABLE IF NOT EXISTS rooms (
  room_id int(11) NOT NULL AUTO_INCREMENT,
  fk_user_id int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  description varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  location varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  building varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  floor varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  capacity int(11) NOT NULL DEFAULT '0',
  facilities varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  status_description varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (room_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'transports'
--

CREATE TABLE IF NOT EXISTS transports (
  transport_id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  description varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  transport_type int(2) NOT NULL DEFAULT '0',
  capacity int(5) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  status_description text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (transport_id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'transport_types'
--

CREATE TABLE IF NOT EXISTS transport_types (
  transport_type_id int(11) NOT NULL AUTO_INCREMENT,
  transport_type_name varchar(32) NOT NULL,
  transport_type_cargo varchar(10) NOT NULL,
  PRIMARY KEY (transport_type_id),
  UNIQUE KEY transport_type_name (transport_type_name)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'users'
--

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(50) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  email varchar(100) COLLATE utf8_bin NOT NULL,
  user_level tinyint(1) NOT NULL DEFAULT '0',
  activated tinyint(1) NOT NULL DEFAULT '1',
  banned tinyint(1) NOT NULL DEFAULT '0',
  ban_reason varchar(255) COLLATE utf8_bin DEFAULT NULL,
  deleted tinyint(1) NOT NULL,
  new_password_key varchar(50) COLLATE utf8_bin DEFAULT NULL,
  new_password_requested datetime DEFAULT NULL,
  new_email varchar(100) COLLATE utf8_bin DEFAULT NULL,
  new_email_key varchar(50) COLLATE utf8_bin DEFAULT NULL,
  last_ip varchar(40) COLLATE utf8_bin NOT NULL,
  last_login datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'user_autologin'
--

CREATE TABLE IF NOT EXISTS user_autologin (
  key_id char(32) COLLATE utf8_bin NOT NULL,
  user_id int(11) NOT NULL DEFAULT '0',
  user_agent varchar(150) COLLATE utf8_bin NOT NULL,
  last_ip varchar(40) COLLATE utf8_bin NOT NULL,
  last_login timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (key_id,user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'user_profiles'
--

CREATE TABLE IF NOT EXISTS user_profiles (
  user_profiles_id int(11) NOT NULL AUTO_INCREMENT,
  fk_user_id int(11) NOT NULL DEFAULT '0',
  full_name varchar(64) COLLATE utf8_bin DEFAULT '',
  position_name varchar(64) COLLATE utf8_bin DEFAULT NULL,
  position_level varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  department_name varchar(128) COLLATE utf8_bin DEFAULT NULL,
  contact_office varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  contact_office_ext varchar(4) COLLATE utf8_bin NOT NULL,
  contact_mobile varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (user_profiles_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

SET foreign_key_checks = 1;
