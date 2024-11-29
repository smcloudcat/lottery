-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-11-29 23:31:44
-- 服务器版本： 5.6.50-log
-- PHP 版本： 8.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `choujiang_frepag`
--

-- --------------------------------------------------------

--
-- 表的结构 `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `announcement` text NOT NULL,
  `title` text NOT NULL,
  `keywords` text NOT NULL,
  `description` text NOT NULL,
  `foot` text NOT NULL,
  `logo` text NOT NULL,
  `name` text NOT NULL,
  `avatar` text NOT NULL,
  `emailsend` int(1) NOT NULL,
  `emailtype` int(11) NOT NULL,
  `stmp` text NOT NULL,
  `port` int(100) NOT NULL,
  `sec` text NOT NULL,
  `emailname` text NOT NULL,
  `emailpass` text NOT NULL,
  `cfcode` int(1) NOT NULL,
  `sitekey` text NOT NULL,
  `secretKey` text NOT NULL,
  `allowemail` text NOT NULL,
  `update1` text NOT NULL COMMENT '预留以后版本的',
  `update2` text NOT NULL COMMENT '预留下个版本的',
  `update3` text NOT NULL COMMENT '预留下个版本的'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `announcement`, `title`, `keywords`, `description`, `foot`, `logo`, `name`, `avatar`, `emailsend`, `emailtype`, `stmp`, `port`, `sec`, `emailname`, `emailpass`, `cfcode`, `sitekey`, `secretKey`, `allowemail`, `update1`, `update2`, `update3`) VALUES
(1, 'admin', '14e1b600b1fd579f47433b88e8d85291', '抽奖请备注你的联系方式<br><font  color=\"red\">每个邮箱每天可以抽奖两次</font>，仅支持qq邮箱哦，如果你多邮箱我无话可说类的<br>公告支持html', '小猫咪抽奖系统', '年会抽奖系统,节日抽奖系统,双十一活动,618活动,双十二活动', '一款开源免费的php抽奖系统，可用于年会抽奖，节日抽奖等等，支持自定义奖品概率和数量，页面简介美观，操作容易', '<p class=\"copyright\">Copyright &copy; 2024. <a target=\"_blank\" href=\"https://lwcat.cn\">小猫咪抽奖系统</a> All rights reserved.</p>', 'https://lwcat.cn/usr/uploads/2024/11/162820287.png', '云猫', 'https://lwcat.cn/usr/uploads/2024/11/2355151515.jpg', 0, 1, 'smtp.163.com', 465, 'ssl', 'xiccsend@163.com', '123456', 0, '123456', '1234567', '*', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `lottery_limits`
--

CREATE TABLE `lottery_limits` (
  `id` int(11) NOT NULL,
  `daily_limit` int(11) DEFAULT '0',
  `total_limit` int(11) DEFAULT '0',
  `draw_count` int(11) DEFAULT '0',
  `update21` text NOT NULL COMMENT '预留下个版本的',
  `update22` text NOT NULL COMMENT '预留下个版本的',
  `update23` text NOT NULL COMMENT '预留下个版本的'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `lottery_limits`
--

INSERT INTO `lottery_limits` (`id`, `daily_limit`, `total_limit`, `draw_count`, `update21`, `update22`, `update23`) VALUES
(1, 2, 10, 25, '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `lottery_logs`
--

CREATE TABLE `lottery_logs` (
  `id` int(11) NOT NULL,
  `account` varchar(100) NOT NULL,
  `prize_id` int(11) DEFAULT NULL,
  `message` text,
  `date` date NOT NULL,
  `update31` text NOT NULL COMMENT '预留下个版本的',
  `update32` text NOT NULL COMMENT '预留下个版本的',
  `update33` text NOT NULL COMMENT '预留下个版本的'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `prizes`
--

CREATE TABLE `prizes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `probability` float NOT NULL,
  `total` int(11) DEFAULT '0',
  `remaining` int(11) DEFAULT '0',
  `update41` text NOT NULL COMMENT '预留下个版本的',
  `update42` text NOT NULL COMMENT '预留下个版本的',
  `update43` text NOT NULL COMMENT '预留下个版本的'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `prizes`
--

INSERT INTO `prizes` (`id`, `name`, `probability`, `total`, `remaining`, `update41`, `update42`, `update43`) VALUES
(4, '一等奖', 0.05, 6, 5, '', '', ''),
(6, '二等奖', 0.25, 999, 974, '', '', ''),
(7, '三等奖', 0.3, 100, 100, '', '', '');

--
-- 转储表的索引
--

--
-- 表的索引 `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `lottery_limits`
--
ALTER TABLE `lottery_limits`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `lottery_logs`
--
ALTER TABLE `lottery_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prize_id` (`prize_id`);

--
-- 表的索引 `prizes`
--
ALTER TABLE `prizes`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `lottery_limits`
--
ALTER TABLE `lottery_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `lottery_logs`
--
ALTER TABLE `lottery_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- 使用表AUTO_INCREMENT `prizes`
--
ALTER TABLE `prizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 限制导出的表
--

--
-- 限制表 `lottery_logs`
--
ALTER TABLE `lottery_logs`
  ADD CONSTRAINT `lottery_logs_ibfk_1` FOREIGN KEY (`prize_id`) REFERENCES `prizes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
