-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 11 Avril 2011 à 22:24
-- Version du serveur: 5.5.8
-- Version de PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `gestion`
--

--
-- Contenu de la table `editor`
--

INSERT INTO `editor` (`k`, `username`, `password`, `longname`, `lang`, `admin`, `active`) VALUES
(1, 'root', '*0C55522581BFAA17B64291B752F270557208F947', 'L''administrateur', 'fr', 1, 1),
(2, 'eric', '*FF6CB53B9CD1A421E3AA6C7720B68BFBE4C5C585', 'Eric Barolet', 'fr', 0, 1),
(3, 'julie', '*E1503F4946889626424D70B71A3D5DC60EF4F253', 'Julie Larochelle', 'en', 0, 1),
(4, 'william', '*8F6F7FB182AFBC84CC78B9B7D59165F7E4ADA60E', 'William Barolet', 'fr', 0, 1),
(5, 'olivier', '*F8D17C1106DA73447086D066F77698CB312426C6', 'Olivier Barolet', 'fr', 0, 1),
(6, 'bruno', '*3A6C7514AC1295BA4F2E8CDD1D7F43E2B2234994', 'Bruno Barolet', 'fr', 0, 1),
(7, 'suzy', '*16E3EA3E9F9816724DED68EAC568A6672E43B970', 'Suzy Proteau', 'fr', 0, 1),
(8, 'jonathan', '*7AFE91D040BEEE385E0757D4379FC6617928CCF1', 'Jonathan Barolet', 'fr', 0, 1),
(9, 'yannick', '*AC96A9DFF6879BEBDC0CD207AD6FA4BDD116F8AB', 'Yannick Barolet', 'fr', 0, 1),
(10, 'marcantoine', '*D90B535104CEC0CBC50087C0CB87AD2D826548F0', 'Marc-Antoine Barolet', 'fr', 0, 1),
(11, 'emmy', '*1E44F24C112BA719A3A15B995EAB5940E60FC53B', 'Emmy Barolet', 'fr', 0, 1),
(12, 'claudie', '*5C69BE18F423526743560DDD225A82A39C87C04B', 'Claudie Turcotte', 'fr', 0, 1);
