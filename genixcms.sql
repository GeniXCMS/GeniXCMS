-- phpMyAdmin SQL Dump
-- version 4.2.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 18, 2015 at 02:15 AM
-- Server version: 10.1.0-MariaDB
-- PHP Version: 5.6.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `genixcms`
--

-- --------------------------------------------------------

--
-- Table structure for table `cat`
--

CREATE TABLE IF NOT EXISTS `cat` (
`id` int(11) NOT NULL,
  `name` text NOT NULL,
  `slug` text NOT NULL,
  `parent` text NOT NULL,
  `desc` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cat`
--

INSERT INTO `cat` (`id`, `name`, `slug`, `parent`, `desc`) VALUES
(1, 'News', 'news', '', ''),
(3, 'Article', 'article', '', ''),
(5, 'Movies', 'movies', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
`id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `menuid` varchar(32) NOT NULL,
  `parent` varchar(11) NOT NULL,
  `sub` enum('0','1') NOT NULL,
  `type` varchar(8) NOT NULL,
  `value` text NOT NULL,
  `class` varchar(64) NOT NULL,
  `order` varchar(4) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `menuid`, `parent`, `sub`, `type`, `value`, `class`, `order`) VALUES
(2, 'Home', 'mainmenu', '', '0', 'custom', 'http://localhost/genixcms', 'blog-nav-item', '1'),
(3, 'News', 'mainmenu', '', '0', 'cat', '1', 'blog-nav-item', '3'),
(4, 'Article', 'mainmenu', '', '0', 'cat', '3', 'blog-nav-item', '4'),
(10, 'Privacy Policy', 'mainmenu', '', '0', 'page', '', 'blog-nav-item', '6'),
(11, 'About Us', 'mainmenu', '', '0', 'page', '19', '', '2'),
(12, 'Disclaimer', 'mainmenu', '', '0', 'page', '120', '', '5');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
`id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 NOT NULL,
  `value` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `name`, `value`) VALUES
(1, 'sitename', 'GeniXCMS'),
(2, 'siteurl', 'http://localhost/genixcms/'),
(3, 'sitedomain', 'localhost/genixcms/'),
(4, 'siteslogan', 'Simple yet Powerful CMS'),
(5, 'sitedesc', 'Keywords2'),
(6, 'sitekeywords', 'keywords, keywords2,  keywords2,  keywords2,  keywords2,  keywords2, '),
(7, 'siteicon', 'favicon.ico'),
(8, 'siteaddress', ''),
(9, 'siteemail', 'site@emailplease.com'),
(10, 'fbacc', ''),
(11, 'fbpage', 'https://fb.me/metalgenix'),
(12, 'twitter', 'genixcms'),
(13, 'linkedin', ''),
(14, 'gplus', ''),
(15, 'logo', '/assets/images/genixcms-logo-small.png'),
(16, 'logourl', ''),
(17, 'is_logourl', 'off'),
(18, 'currency', 'USD'),
(19, 'country_id', 'ID'),
(20, 'mailtype', '1'),
(21, 'smtphost', 'mx1.mtgx.us'),
(22, 'smtpuser', 'admin'),
(23, 'smtppass', 'admin'),
(24, 'smtpssl', '0'),
(25, 'timezone', '+7'),
(26, 'paypalemail', ''),
(27, 'robots', 'index, follow'),
(28, 'use_jquery', 'on'),
(29, 'use_bootstrap', 'on'),
(30, 'use_fontawesome', 'on'),
(31, 'use_bsvalidator', 'on'),
(32, 'jquery_v', '1.11.1'),
(33, 'bs_v', ''),
(34, 'fontawesome_v', ''),
(35, 'use_editor', 'on'),
(36, 'editor_type', 'summernote'),
(37, 'editor_v', ''),
(39, 'menus', '{"mainmenu":{"name":"Main Menu","class":"","menu":[]},"footer":{"name":"Footer Menu","class":"","menu":[{"parent":"","menuid":"footer","type":"custom","value":"http://localhost/genixcms"},{"parent":"","menuid":"footer","type":"cat","value":"1"}]}}'),
(43, 'post_perpage', '12'),
(44, 'pagination', 'pager'),
(45, 'pinger', 'rpc.pingomatic.com'),
(46, 'bsvalidator_v', ''),
(47, 'ppsandbox', 'off'),
(48, 'ppuser', ''),
(49, 'pppass', ''),
(50, 'ppsign', '');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
`id` bigint(32) NOT NULL,
  `date` datetime NOT NULL,
  `title` text NOT NULL,
  `slug` text NOT NULL,
  `content` mediumtext NOT NULL,
  `author` text NOT NULL,
  `type` text NOT NULL,
  `cat` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `status` enum('0','1','2') NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8;



--
-- Table structure for table `posts_param`
--

CREATE TABLE IF NOT EXISTS `posts_param` (
`id` bigint(32) NOT NULL,
  `post_id` bigint(32) NOT NULL,
  `param` text NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` bigint(32) NOT NULL,
  `userid` varchar(16) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `confirm` varchar(255) NOT NULL,
  `group` enum('0','1','2','3','4','5') NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `userid`, `pass`, `confirm`, `group`, `email`) VALUES
(1, 'admin', '15731ad2ebe0f218cc95978ef90d354c', '', '0', 'admin@metalgenix.com'),
(2, 'hugup', 'febe39656c373f54e53f876e07610f4b', '', '4', 'hugup@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `user_detail`
--

CREATE TABLE IF NOT EXISTS `user_detail` (
`id` bigint(20) NOT NULL,
  `userid` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `fname` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `lname` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `sex` varchar(2) COLLATE latin1_general_ci NOT NULL,
  `birthplace` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `birthdate` date NOT NULL,
  `addr` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `city` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `state` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `country` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `postcode` varchar(32) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `user_detail`
--

INSERT INTO `user_detail` (`id`, `userid`, `fname`, `lname`, `sex`, `birthplace`, `birthdate`, `addr`, `city`, `state`, `country`, `postcode`) VALUES
(1, 'admin', 'Admin', 'Istrator', 'm', 'Madiun', '0000-00-00', '', '', '', '', ''),
(2, 'hugup', '', '', '', '', '0000-00-00', '', '', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cat`
--
ALTER TABLE `cat`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts_param`
--
ALTER TABLE `posts_param`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_detail`
--
ALTER TABLE `user_detail`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cat`
--
ALTER TABLE `cat`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=51;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=122;
--
-- AUTO_INCREMENT for table `posts_param`
--
ALTER TABLE `posts_param`
MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `user_detail`
--
ALTER TABLE `user_detail`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
