-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2026 at 11:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_magazine-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblacademicyear`
--

CREATE TABLE `tblacademicyear` (
  `academicyearid` int(11) NOT NULL,
  `yearname` varchar(50) NOT NULL,
  `submission_closure_date` date NOT NULL,
  `final_closure_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblactivity_log`
--

CREATE TABLE `tblactivity_log` (
  `logid` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `page_url` varchar(255) NOT NULL,
  `browser_info` varchar(255) NOT NULL,
  `access_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `categoryid` int(11) NOT NULL,
  `academicyearid` int(11) NOT NULL,
  `categoryname` varchar(100) NOT NULL,
  `categorystartdate` date NOT NULL,
  `categoryclosuredate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcomment`
--

CREATE TABLE `tblcomment` (
  `commentid` int(11) NOT NULL,
  `contributionid` int(11) NOT NULL,
  `coordinatorid` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcontribution`
--

CREATE TABLE `tblcontribution` (
  `contributionid` int(11) NOT NULL,
  `studentid` int(11) NOT NULL,
  `categoryid` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `submission_date` date NOT NULL,
  `status` enum('draft','submitted','selected','rejected') NOT NULL DEFAULT 'draft',
  `is_selected_for_publication` tinyint(1) DEFAULT 0,
  `selected_by` int(11) DEFAULT NULL,
  `selecteddate` date DEFAULT NULL,
  `filepath1` varchar(255) DEFAULT NULL,
  `filepath2` varchar(255) DEFAULT NULL,
  `filepath3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblfaculty`
--

CREATE TABLE `tblfaculty` (
  `facultyid` int(11) NOT NULL,
  `facultyname` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblfaculty`
--

INSERT INTO `tblfaculty` (`facultyid`, `facultyname`, `description`) VALUES
(1, 'Faculty of Computing and IT', 'Focuses on software engineering, computer science, cybersecurity, and information systems.'),
(2, 'Faculty of Engineering and Science', 'Includes civil, mechanical, and electrical engineering, as well as physics and chemistry.');

-- --------------------------------------------------------

--
-- Table structure for table `tblnotification_log`
--

CREATE TABLE `tblnotification_log` (
  `notificationid` int(11) NOT NULL,
  `notification_type` enum('new_contribution','new_guest') NOT NULL,
  `contributionid` int(11) DEFAULT NULL,
  `reference_userid` int(11) DEFAULT NULL,
  `sent_to` int(11) NOT NULL,
  `sent_date` date NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblrole`
--

CREATE TABLE `tblrole` (
  `roleid` int(11) NOT NULL,
  `rolename` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblrole`
--

INSERT INTO `tblrole` (`roleid`, `rolename`) VALUES
(1, 'administrator'),
(5, 'guest'),
(3, 'marketing_coordinator'),
(2, 'marketing_manager'),
(4, 'student');

-- --------------------------------------------------------

--
-- Table structure for table `tblterms_and_conditions`
--

CREATE TABLE `tblterms_and_conditions` (
  `agreementid` int(11) NOT NULL,
  `studentid` int(11) NOT NULL,
  `academicyearid` int(11) NOT NULL,
  `agreed_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `userid` int(11) NOT NULL,
  `facultyid` int(11) DEFAULT NULL,
  `roleid` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default-avatar.png',
  `created_at` datetime DEFAULT current_timestamp(),
  `previous_login` datetime DEFAULT NULL,
  `current_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`userid`, `facultyid`, `roleid`, `username`, `email`, `password_hash`, `gender`, `profile_picture`, `created_at`, `previous_login`, `current_login`) VALUES
(1, NULL, 1, 'admin', 'admin@university.edu', '$2y$10$si.w4sOnXlMTn72Yv.5XROOK/3iw8bWv.p7fWGDtp.uOeCzAKDFXy', 'other', NULL, '2026-03-13 03:11:17', '2026-03-13 10:15:07', '2026-03-16 15:19:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblacademicyear`
--
ALTER TABLE `tblacademicyear`
  ADD PRIMARY KEY (`academicyearid`);

--
-- Indexes for table `tblactivity_log`
--
ALTER TABLE `tblactivity_log`
  ADD PRIMARY KEY (`logid`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`categoryid`),
  ADD KEY `academicyearid` (`academicyearid`);

--
-- Indexes for table `tblcomment`
--
ALTER TABLE `tblcomment`
  ADD PRIMARY KEY (`commentid`),
  ADD KEY `contributionid` (`contributionid`),
  ADD KEY `coordinatorid` (`coordinatorid`);

--
-- Indexes for table `tblcontribution`
--
ALTER TABLE `tblcontribution`
  ADD PRIMARY KEY (`contributionid`),
  ADD KEY `studentid` (`studentid`),
  ADD KEY `categoryid` (`categoryid`),
  ADD KEY `selected_by` (`selected_by`);

--
-- Indexes for table `tblfaculty`
--
ALTER TABLE `tblfaculty`
  ADD PRIMARY KEY (`facultyid`);

--
-- Indexes for table `tblnotification_log`
--
ALTER TABLE `tblnotification_log`
  ADD PRIMARY KEY (`notificationid`),
  ADD KEY `contributionid` (`contributionid`),
  ADD KEY `reference_userid` (`reference_userid`),
  ADD KEY `sent_to` (`sent_to`);

--
-- Indexes for table `tblrole`
--
ALTER TABLE `tblrole`
  ADD PRIMARY KEY (`roleid`),
  ADD UNIQUE KEY `rolename` (`rolename`);

--
-- Indexes for table `tblterms_and_conditions`
--
ALTER TABLE `tblterms_and_conditions`
  ADD PRIMARY KEY (`agreementid`),
  ADD KEY `studentid` (`studentid`),
  ADD KEY `academicyearid` (`academicyearid`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `facultyid` (`facultyid`),
  ADD KEY `roleid` (`roleid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblacademicyear`
--
ALTER TABLE `tblacademicyear`
  MODIFY `academicyearid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblactivity_log`
--
ALTER TABLE `tblactivity_log`
  MODIFY `logid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `categoryid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcomment`
--
ALTER TABLE `tblcomment`
  MODIFY `commentid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcontribution`
--
ALTER TABLE `tblcontribution`
  MODIFY `contributionid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblfaculty`
--
ALTER TABLE `tblfaculty`
  MODIFY `facultyid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblnotification_log`
--
ALTER TABLE `tblnotification_log`
  MODIFY `notificationid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblrole`
--
ALTER TABLE `tblrole`
  MODIFY `roleid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblterms_and_conditions`
--
ALTER TABLE `tblterms_and_conditions`
  MODIFY `agreementid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblactivity_log`
--
ALTER TABLE `tblactivity_log`
  ADD CONSTRAINT `tblactivity_log_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `tbluser` (`userid`);

--
-- Constraints for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD CONSTRAINT `tblcategory_ibfk_1` FOREIGN KEY (`academicyearid`) REFERENCES `tblacademicyear` (`academicyearid`);

--
-- Constraints for table `tblcomment`
--
ALTER TABLE `tblcomment`
  ADD CONSTRAINT `tblcomment_ibfk_1` FOREIGN KEY (`contributionid`) REFERENCES `tblcontribution` (`contributionid`),
  ADD CONSTRAINT `tblcomment_ibfk_2` FOREIGN KEY (`coordinatorid`) REFERENCES `tbluser` (`userid`);

--
-- Constraints for table `tblcontribution`
--
ALTER TABLE `tblcontribution`
  ADD CONSTRAINT `tblcontribution_ibfk_1` FOREIGN KEY (`studentid`) REFERENCES `tbluser` (`userid`),
  ADD CONSTRAINT `tblcontribution_ibfk_2` FOREIGN KEY (`categoryid`) REFERENCES `tblcategory` (`categoryid`),
  ADD CONSTRAINT `tblcontribution_ibfk_3` FOREIGN KEY (`selected_by`) REFERENCES `tbluser` (`userid`);

--
-- Constraints for table `tblnotification_log`
--
ALTER TABLE `tblnotification_log`
  ADD CONSTRAINT `tblnotification_log_ibfk_1` FOREIGN KEY (`contributionid`) REFERENCES `tblcontribution` (`contributionid`),
  ADD CONSTRAINT `tblnotification_log_ibfk_2` FOREIGN KEY (`reference_userid`) REFERENCES `tbluser` (`userid`),
  ADD CONSTRAINT `tblnotification_log_ibfk_3` FOREIGN KEY (`sent_to`) REFERENCES `tbluser` (`userid`);

--
-- Constraints for table `tblterms_and_conditions`
--
ALTER TABLE `tblterms_and_conditions`
  ADD CONSTRAINT `tblterms_and_conditions_ibfk_1` FOREIGN KEY (`studentid`) REFERENCES `tbluser` (`userid`),
  ADD CONSTRAINT `tblterms_and_conditions_ibfk_2` FOREIGN KEY (`academicyearid`) REFERENCES `tblacademicyear` (`academicyearid`);

--
-- Constraints for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD CONSTRAINT `tbluser_ibfk_1` FOREIGN KEY (`facultyid`) REFERENCES `tblfaculty` (`facultyid`),
  ADD CONSTRAINT `tbluser_ibfk_2` FOREIGN KEY (`roleid`) REFERENCES `tblrole` (`roleid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
