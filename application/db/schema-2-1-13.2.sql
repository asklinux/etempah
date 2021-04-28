-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jan 02, 2013 at 11:46 PM
-- Server version: 5.1.66
-- PHP Version: 5.3.6-13ubuntu3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `stbmci`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE IF NOT EXISTS `booking` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(11) NOT NULL DEFAULT '0',
  `booking_ref_no` varchar(16) NOT NULL DEFAULT '',
  `booking_type` int(1) NOT NULL DEFAULT '0',
  `full_day` tinyint(1) NOT NULL DEFAULT '0',
  `fk_booking_id` int(11) NOT NULL DEFAULT '0',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `booking_status` int(1) NOT NULL DEFAULT '0',
  `booking_status_description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `fk_user_id`, `booking_type`, `full_day`, `fk_booking_id`, `start_date`, `end_date`, `booking_status`, `booking_status_description`, `created`, `modified`) VALUES
(1, 0, 1, 0, 1, '2013-01-03 09:00:00', '2013-01-03 17:00:00', 2, 'Sheikdh tidak benarkan mesyuarat ini dijalankan, beliau tidak mahu hadir, oleh yang demikian, perihal ini membawa kepada ketidakhadiran nya ke mari. Jadi, sesungguhnya, mungkin akan menjadi satu kepentingan bagi kita untuk tidak membuang masa menulis cata', '2013-01-02 12:09:33', '2013-01-02 07:32:32'),
(2, 0, 2, 0, 2, '2013-01-03 00:00:00', '0000-00-00 00:00:00', 1, '', '2013-01-02 13:42:52', '2013-01-02 06:40:12'),
(3, 1, 3, 0, 2, '2013-01-03 09:00:00', '2013-01-03 18:00:00', 1, '', '2013-01-02 13:43:36', '2013-01-02 06:40:18'),
(4, 1, 4, 0, 1, '2013-01-07 04:00:00', '2013-01-07 04:00:00', 1, '', '2013-01-02 14:22:05', '2013-01-02 06:33:31'),
(5, 0, 1, 0, 2, '2013-01-03 00:00:00', '2013-01-16 00:30:00', 0, '', '2013-01-02 15:19:02', '2013-01-02 07:19:03');

-- --------------------------------------------------------

--
-- Table structure for table `booking_equipments`
--

CREATE TABLE IF NOT EXISTS `booking_equipments` (
  `booking_equipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_with_room` tinyint(1) NOT NULL,
  `equipment_purpose` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `place` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `require_technical_person` int(4) NOT NULL DEFAULT '0',
  `booking_equipment_description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `equipment_list` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`booking_equipment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `booking_equipments`
--

INSERT INTO `booking_equipments` (`booking_equipment_id`, `booking_with_room`, `equipment_purpose`, `place`, `require_technical_person`, `booking_equipment_description`, `equipment_list`) VALUES
(1, 1, '', '4', 0, '', '1:1,2:1'),
(2, 1, 'Sesi UAT MyBooking', 'Bilik Mesyuarat 6, aras 2 blok B2 Cyberjaya', 0, '', '1:1,2:1,3:0,4:0,5:0,6:0'),
(3, 1, '', '8', 1, 'En. Syazwan perlu ada untuk set up segala peralatan tersebut. Sekiranya beliau tidak mendengar perin', '1:9,2:9,3:9,4:9,5:9,6:9');

-- --------------------------------------------------------

--
-- Table structure for table `booking_foods`
--

CREATE TABLE IF NOT EXISTS `booking_foods` (
  `booking_food_id` int(11) NOT NULL AUTO_INCREMENT,
  `food_type_ids` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `place` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `food_purpose` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `total_pack` int(2) NOT NULL DEFAULT '0',
  `booking_food_description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`booking_food_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `booking_foods`
--

INSERT INTO `booking_foods` (`booking_food_id`, `food_type_ids`, `place`, `food_purpose`, `total_pack`, `booking_food_description`) VALUES
(1, '1,2,3', '4', '', 10, ''),
(2, '1,2,3', 'Bilik Mesyuarat 6, aras 2 blok B2 Cyberjaya', 'Makanan untuk sesi UAT produk OSCC', 12, ''),
(3, '1,4', '8', '', 750, 'Makanan perlulah disediakan dengan teliti. Mohon ikan dibuang insang dan perut sebelum masak. Kegaga');

-- --------------------------------------------------------

--
-- Table structure for table `booking_rooms`
--

CREATE TABLE IF NOT EXISTS `booking_rooms` (
  `booking_room_id` int(11) NOT NULL AUTO_INCREMENT,
  `secretariat` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `chairman` varchar(64) COLLATE utf8_bin NOT NULL,
  `room_purpose` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `total_from_agensi` int(4) NOT NULL DEFAULT '0',
  `total_from_nonagensi` int(4) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `fk_room_id` int(4) NOT NULL DEFAULT '0',
  `fk_booking_equipment_id` int(4) NOT NULL DEFAULT '0',
  `fk_booking_food_id` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`booking_room_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `booking_rooms`
--

INSERT INTO `booking_rooms` (`booking_room_id`, `secretariat`, `chairman`, `room_purpose`, `total_from_agensi`, `total_from_nonagensi`, `description`, `fk_room_id`, `fk_booking_equipment_id`, `fk_booking_food_id`) VALUES
(1, 'Mohd Hasni Salleh', 'Mohd Hasni Ismail', 'Mesyuarat Jawatankuasa A Bil. 1', 10, 0, '', 4, 1, 1),
(2, 'Sheikh Mohd Rozaimi', 'Faizal Kamil', 'Mesyuarat Agung Umno', 12, 350, 'Sheikh tidak mahu menjadi pengerusi tetapi dipaksa oleh pengerusi, Tan Sri Faizal bin Kamil. Alhamdu', 8, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `booking_transports`
--

CREATE TABLE IF NOT EXISTS `booking_transports` (
  `booking_transport_id` int(11) NOT NULL AUTO_INCREMENT,
  `destination_from` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `destination_to` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `transport_purpose` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `total_passenger` int(2) NOT NULL DEFAULT '0',
  `trip_type` int(1) NOT NULL DEFAULT '0',
  `transports` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `booking_transport_description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`booking_transport_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Dumping data for table `booking_transports`
--

INSERT INTO `booking_transports` (`booking_transport_id`, `destination_from`, `destination_to`, `transport_purpose`, `total_passenger`, `trip_type`, `transports`, `booking_transport_description`) VALUES
(1, 'MAMPU, Cyberjaya', 'Hotel Corus Paradise, Port Dickson', 'Lawatan', 3, 1, '1:1,2:0,3:0,4:0,5:0', '');

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `user_agent` varchar(150) COLLATE utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('997d04ad90927012f008931dbea91d7a', '127.0.0.1', 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/537.4', 1357141480, 'a:5:{s:9:"user_data";s:0:"";s:7:"user_id";s:1:"1";s:8:"username";s:5:"admin";s:10:"user_level";s:1:"1";s:6:"status";s:1:"1";}');

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE IF NOT EXISTS `destinations` (
  `destination_name` varchar(128) DEFAULT NULL,
  UNIQUE KEY `destination_name` (`destination_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`destination_name`) VALUES
('Hotel Corus Paradise, Port Dickson'),
('MAMPU, Cyberjaya');

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE IF NOT EXISTS `equipments` (
  `equipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `quantity` int(5) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`equipment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Dumping data for table `equipments`
--

INSERT INTO `equipments` (`equipment_id`, `name`, `description`, `quantity`, `deleted`) VALUES
(1, 'Projektor', '', 10, 0),
(2, 'Whiteboard', '', 12, 0),
(3, 'Broadband (Digi)', '', 8, 0),
(4, 'Wireless Pointer', '', 10, 0),
(5, 'Komputer Riba', '', 25, 0),
(6, 'Meja Tambahan', 'Meja panjang 3 kaki x 8 kaki', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(40) COLLATE utf8_bin NOT NULL,
  `login` varchar(50) COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE IF NOT EXISTS `rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `location` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `building` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `floor` varchar(5) COLLATE utf8_bin NOT NULL DEFAULT '',
  `capacity` int(11) NOT NULL DEFAULT '0',
  `facilities` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `status_description` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `fk_user_id`, `name`, `description`, `location`, `building`, `floor`, `capacity`, `facilities`, `status`, `status_description`) VALUES
(2, 2, 'Bilik Mesyuarat Cyber 2', '', 'Cyberjaya', 'B', '2', 30, 'Projektor, Whiteboard', 1, ''),
(3, 2, 'Bilik Latihan', 'Bilik Latihan OSCC', 'Cyberjaya', 'B', '2', 28, 'Komputer, Whiteboard, Projektor', 1, ''),
(4, 1, 'Bilik Perbincangan Cyber Utara', 'Bilik kecil', 'Cyberjaya', 'B', '1', 10, 'Whiteboard', 0, 'Tidak Kemas, cleaner cuti.\n'),
(5, 1, 'Bilik Mesyuarat 6', '', 'Cyberjaya', 'B2', '2', 25, 'Whiteboard', 1, ''),
(6, 3, 'Bilik Perbincangan Cyber Utara', 'Bilik besar', 'Putrajaya', 'B', '3', 10, '', 1, ''),
(7, 1, 'Auditorium 1', '', 'Cyberjaya', 'B2', '1', 70, '', 1, ''),
(8, 1, 'Auditorium Hall', 'Siti Nurhaliza pernah buat persembahan di sini.', 'Johor Baharu, Johor', 'Bungkusan 5', '13A', 250, '', 0, 'Tahi lalat banyak, terlalu kotor untuk berjoget.');

-- --------------------------------------------------------

--
-- Table structure for table `transports`
--

CREATE TABLE IF NOT EXISTS `transports` (
  `transport_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `transport_type` int(2) NOT NULL DEFAULT '0',
  `capacity` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`transport_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Dumping data for table `transports`
--

INSERT INTO `transports` (`transport_id`, `name`, `description`, `transport_type`, `capacity`) VALUES
(1, 'Proton Perdana V6', 'WXY123', 1, 5),
(2, 'Naza Ria', 'WXY1288', 2, 7),
(3, 'Toyota Fortuner', 'WXY125', 4, 5),
(4, 'Naza Ria', 'WXY126', 2, 7),
(6, 'Proton Perdana V6', 'WVF3312', 1, 4),
(7, 'Scania', 'WJK 1433', 5, 44),
(8, 'Nissan NV200', '', 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `user_level` tinyint(1) NOT NULL DEFAULT '0',
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `ban_reason` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL,
  `new_password_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `new_password_requested` datetime DEFAULT NULL,
  `new_email` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `new_email_key` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=53 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `user_level`, `activated`, `banned`, `ban_reason`, `deleted`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES
(1, 'admin', '$2a$08$NQrwm.tXDOyR6pED/DylFOX5Etp4KFe5pSRhluwhKLe94G08aKGYS', 'mhi@oscc.org.my', 1, 1, 0, NULL, 0, NULL, NULL, NULL, '65ffb46c8b3bb4f580cbaf660c14ecf2', '127.0.0.1', '2013-01-02 23:05:33', '2012-11-12 12:05:14', '2013-01-02 15:05:33'),
(40, 'faizal', '$2a$08$pUTl3MKzaCfrfQ2xhCQbBeaSYVweXL2.R1Eovg1gNahDkwNatB12G', 'faizal@oscc.org.my', 0, 1, 0, NULL, 1, NULL, NULL, NULL, NULL, '10.17.238.217', '2012-12-20 11:24:20', '2012-12-20 11:23:29', '2012-12-20 08:22:47'),
(52, 'mohdhasni.ismail', '$2a$08$xrjyD9QS3tMV6vIBus/sBuh/XJYeKL6F63yfvHB4ywSxVrNo73fp2', 'mohdhasni.ismail@gmail.com', 0, 1, 0, NULL, 0, NULL, NULL, NULL, NULL, '127.0.0.1', '2013-01-02 22:59:20', '2013-01-02 22:58:53', '2013-01-02 14:59:20');

-- --------------------------------------------------------

--
-- Table structure for table `user_autologin`
--

CREATE TABLE IF NOT EXISTS `user_autologin` (
  `key_id` char(32) COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_agent` varchar(150) COLLATE utf8_bin NOT NULL,
  `last_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(11) NOT NULL DEFAULT '0',
  `full_name` varchar(64) COLLATE utf8_bin DEFAULT '',
  `position_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `position_level` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `department_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `contact_office` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  `contact_office_ext` varchar(4) COLLATE utf8_bin NOT NULL,
  `contact_mobile` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=46 ;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `fk_user_id`, `full_name`, `position_name`, `position_level`, `department_name`, `contact_office`, `contact_office_ext`, `contact_mobile`) VALUES
(1, 1, 'Mohd Hasni Salleh', 'Penolong Pengarah', 'Jusa A', 'Seksyen Teknologi Maklumat', '03-88887224', '1523', ''),
(2, 2, 'Jamalulkhaer Jamaluddin', '12', '', 'OSCC', '', '', ''),
(3, 3, 'Thaibah bte Ishak', '13', '', 'OSCC', '', '', ''),
(45, 52, 'Mohd Hasni Ismail', 'Pengarah Bahagian', 'F58', 'OSCC', '03-88887224', '1523', '');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;