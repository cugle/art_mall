-- MySQL dump 10.9
--
-- Host: localhost    Database: aaa
-- ------------------------------------------------------
-- Server version	4.1.22-community-nt

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `oun_account_log`
--

LOCK TABLES `oun_account_log` WRITE;
/*!40000 ALTER TABLE `oun_account_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_account_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_ad`
--

LOCK TABLES `oun_ad` WRITE;
/*!40000 ALTER TABLE `oun_ad` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_ad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_ad_affiche`
--

LOCK TABLES `oun_ad_affiche` WRITE;
/*!40000 ALTER TABLE `oun_ad_affiche` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_ad_affiche` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_ad_position`
--

LOCK TABLES `oun_ad_position` WRITE;
/*!40000 ALTER TABLE `oun_ad_position` DISABLE KEYS */;
INSERT INTO `oun_ad_position` VALUES (26,'首页banner',910,60,'','',0,1),(27,'底部通栏',1024,80,'','',0,1);
/*!40000 ALTER TABLE `oun_ad_position` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_admin_user`
--

LOCK TABLES `oun_admin_user` WRITE;
/*!40000 ALTER TABLE `oun_admin_user` DISABLE KEYS */;
INSERT INTO `oun_admin_user` VALUES (1,'admin','',1358403151,1368840619,'127.0.0.1','all','','','',0,5,NULL,0,1);
/*!40000 ALTER TABLE `oun_admin_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_arti_attr`
--

LOCK TABLES `oun_arti_attr` WRITE;
/*!40000 ALTER TABLE `oun_arti_attr` DISABLE KEYS */;
INSERT INTO `oun_arti_attr` VALUES (1,'默认属性',1),(3,'内部文章',268),(4,'外部新闻',268),(5,'默认属性',312),(6,'默认属性',313),(7,'默认属性',314),(8,'默认属性',315),(9,'默认属性',316),(10,'默认属性',317),(11,'默认属性',318),(12,'默认属性',319),(13,'默认属性',320),(14,'默认属性',321),(15,'默认属性',322),(16,'默认属性',323),(17,'默认属性',324),(18,'默认属性',325);
/*!40000 ALTER TABLE `oun_arti_attr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_arti_comms`
--

LOCK TABLES `oun_arti_comms` WRITE;
/*!40000 ALTER TABLE `oun_arti_comms` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_arti_comms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_arti_comms_re`
--

LOCK TABLES `oun_arti_comms_re` WRITE;
/*!40000 ALTER TABLE `oun_arti_comms_re` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_arti_comms_re` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_arti_file`
--

LOCK TABLES `oun_arti_file` WRITE;
/*!40000 ALTER TABLE `oun_arti_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_arti_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_arti_tag`
--

LOCK TABLES `oun_arti_tag` WRITE;
/*!40000 ALTER TABLE `oun_arti_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_arti_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_articat`
--

LOCK TABLES `oun_articat` WRITE;
/*!40000 ALTER TABLE `oun_articat` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_articat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_article`
--

LOCK TABLES `oun_article` WRITE;
/*!40000 ALTER TABLE `oun_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_artitxt`
--

LOCK TABLES `oun_artitxt` WRITE;
/*!40000 ALTER TABLE `oun_artitxt` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_artitxt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_artitxt_ip`
--

LOCK TABLES `oun_artitxt_ip` WRITE;
/*!40000 ALTER TABLE `oun_artitxt_ip` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_artitxt_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_carts`
--

LOCK TABLES `oun_carts` WRITE;
/*!40000 ALTER TABLE `oun_carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_citycat`
--

LOCK TABLES `oun_citycat` WRITE;
/*!40000 ALTER TABLE `oun_citycat` DISABLE KEYS */;
INSERT INTO `oun_citycat` VALUES (1,0,'404,405,406,407,408,409,410,411,412,413,414,415,416,417','北京','',1,1),(2,0,'430,431,432,433,434,435,436,437,438,439,440','上海','',1,1),(3,0,'418,419,420,421,422,423,424,425,426,427,428,429','天津','',1,1),(4,0,'441,442,443,444','广州','',1,1),(5,0,'371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,','重庆','',1,1),(6,0,'','深圳','',1,1),(7,0,'34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50','山东','',1,1),(8,0,'51,52,53,54,55,56,57,58,59','福建','',1,1),(9,0,'60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76','安徽','',1,1),(10,0,'77,78,79,80,81,82,83,84,85,86,87','浙江','',1,1),(11,0,'88,89,90,91,92,93,94,95,96,97,98','江西','',1,1),(12,0,'99,100,101,102,103,104,105,106,107,108,109,110,111','江苏','',1,1),(13,0,'112,113,114,115,116,117,118,119,120,121,122,123,124,125','湖北','',1,1),(14,0,'126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143','河南','',1,1),(15,0,'144,145,146,147,148,149,150,151,152,153,154,155,156,157','湖南','',1,1),(16,0,'158,159,160,161,162,163,164,165,166,167,168,169','内蒙古','',1,1),(17,0,'170,171,172,173,174,175,176,177,178,179,180','山西','',1,1),(18,0,'181,182,183,184,185,186,187,188,189,190,191','河北','',1,1),(19,0,'192,193,194,195,196,197,198,199,200,201,202,203,204','黑龙江','',1,1),(20,0,'205,206,207,208,209,210,211,212,213','吉林','',1,1),(21,0,'214,215,216,217,218,219,220,221,222,223,224,225,226,227','辽宁','',1,1),(22,0,'228,229,230','海南','',1,1),(23,0,'231,232,233,234,235,236,237,238,239,240,241,242,243,244','广西','',1,1),(24,0,'245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,','广东','',1,1),(25,0,'266,267,268,269,270,271,272','西藏','',1,1),(26,0,'273,274,275,276,277,278,279,280,281','贵州','',1,1),(27,0,'282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,','四川','',1,1),(28,0,'303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318','云南','',1,1),(29,0,'319,320,321,322,323,324,325,326,327,328,329,330,331,332,333','新疆','',1,1),(30,0,'334,335,336,337,338','宁夏','',1,1),(31,0,'339,340,341,342,343,344,345,346','青海','',1,1),(32,0,'347,348,349,350,351,352,353,354,355,356,357,358,359,360','甘肃','',1,1),(33,0,'361,362,363,364,365,366,367,368,369,370','陕西','',1,1),(34,7,'','济南','',1,1),(35,7,'','滨州','',1,1),(36,7,'','德州','',1,1),(37,7,'','东营','',1,1),(38,7,'','菏泽','',1,1),(39,7,'','济宁','',1,1),(40,7,'','莱芜','',1,1),(41,7,'','聊城','',1,1),(42,7,'','临沂','',1,1),(43,7,'','青岛','',1,1),(44,7,'','日照','',1,1),(45,7,'','泰安','',1,1),(46,7,'','潍坊','',1,1),(47,7,'','威海','',1,1),(48,7,'','烟台','',1,1),(49,7,'','枣庄','',1,1),(50,7,'','淄博','',1,1),(51,8,'','福州','',1,1),(52,8,'','龙岩','',1,1),(53,8,'','南平','',1,1),(54,8,'','宁德','',1,1),(55,8,'','莆田','',1,1),(56,8,'','泉州','',1,1),(57,8,'','三明','',1,1),(58,8,'','厦门','',1,1),(59,8,'','漳州','',1,1),(60,9,'','合肥','',1,1),(61,9,'','安庆','',1,1),(62,9,'','蚌埠','',1,1),(63,9,'','亳州','',1,1),(64,9,'','巢湖','',1,1),(65,9,'','池州','',1,1),(66,9,'','滁州','',1,1),(67,9,'','阜阳','',1,1),(68,9,'','淮北','',1,1),(69,9,'','淮南','',1,1),(70,9,'','黄山市','',1,1),(71,9,'','六安','',1,1),(72,9,'','马鞍山','',1,1),(73,9,'','宿州','',1,1),(74,9,'','铜陵','',1,1),(75,9,'','芜湖','',1,1),(76,9,'','宣城','',1,1),(77,10,'','杭州','',1,1),(78,10,'','湖州','',1,1),(79,10,'','嘉兴','',1,1),(80,10,'','金华','',1,1),(81,10,'','丽水','',1,1),(82,10,'','宁波','',1,1),(83,10,'','衢州','',1,1),(84,10,'','绍兴','',1,1),(85,10,'','台州','',1,1),(86,10,'','温州','',1,1),(87,10,'','舟山','',1,1),(88,11,'','南昌','',1,1),(89,11,'','抚州','',1,1),(90,11,'','赣州','',1,1),(91,11,'','吉安','',1,1),(92,11,'','景德镇','',1,1),(93,11,'','九江','',1,1),(94,11,'','上饶','',1,1),(95,11,'','萍乡','',1,1),(96,11,'','新余','',1,1),(97,11,'','宜春','',1,1),(98,11,'','鹰潭','',1,1),(99,12,'','南京','',1,1),(100,12,'','常州','',1,1),(101,12,'','淮安','',1,1),(102,12,'','连云港','',1,1),(103,12,'','南通','',1,1),(104,12,'','宿迁','',1,1),(105,12,'','苏州','',1,1),(106,12,'','泰州','',1,1),(107,12,'','无锡','',1,1),(108,12,'','徐州','',1,1),(109,12,'','盐城','',1,1),(110,12,'','扬州','',1,1),(111,12,'','镇江','',1,1),(112,13,'','武汉','',1,1),(113,13,'','恩施州','',1,1),(114,13,'','鄂州','',1,1),(115,13,'','黄冈','',1,1),(116,13,'','黄石','',1,1),(117,13,'','荆门','',1,1),(118,13,'','荆州','',1,1),(119,13,'','十堰','',1,1),(120,13,'','随州','',1,1),(121,13,'','襄阳','',1,1),(122,13,'','咸宁','',1,1),(123,13,'','孝感','',1,1),(124,13,'','宜昌','',1,1),(125,13,'','省直辖行政单位','',1,1),(126,14,'','郑州','',1,1),(127,14,'','安阳','',1,1),(128,14,'','鹤壁','',1,1),(129,14,'','焦作','',1,1),(130,14,'','济源','',1,1),(131,14,'','开封','',1,1),(132,14,'','漯河','',1,1),(133,14,'','洛阳','',1,1),(134,14,'','南阳','',1,1),(135,14,'','平顶山','',1,1),(136,14,'','濮阳','',1,1),(137,14,'','三门峡','',1,1),(138,14,'','商丘','',1,1),(139,14,'','新乡','',1,1),(140,14,'','信阳','',1,1),(141,14,'','许昌','',1,1),(142,14,'','周口','',1,1),(143,14,'','驻马店','',1,1),(144,15,'','长沙','',1,1),(145,15,'','常德','',1,1),(146,15,'','郴州','',1,1),(147,15,'','衡阳','',1,1),(148,15,'','怀化','',1,1),(149,15,'','娄底','',1,1),(150,15,'','邵阳','',1,1),(151,15,'','湘潭','',1,1),(152,15,'','湘西土家族苗族自治州','',1,1),(153,15,'','益阳','',1,1),(154,15,'','永州','',1,1),(155,15,'','岳阳','',1,1),(156,15,'','张家界','',1,1),(157,15,'','株洲','',1,1),(158,16,'','呼和浩特','',1,1),(159,16,'','阿拉善盟','',1,1),(160,16,'','包头','',1,1),(161,16,'','巴彦淖尔','',1,1),(162,16,'','赤峰','',1,1),(163,16,'','鄂尔多斯','',1,1),(164,16,'','呼伦贝尔','',1,1),(165,16,'','通辽','',1,1),(166,16,'','乌海','',1,1),(167,16,'','乌兰察布','',1,1),(168,16,'','锡林郭勒盟','',1,1),(169,16,'','兴安盟','',1,1),(170,17,'445,446,447,448,449,450','太原','',1,1),(171,17,'','长治','',1,1),(172,17,'','大同','',1,1),(173,17,'','晋城','',1,1),(174,17,'','晋中','',1,1),(175,17,'','临汾','',1,1),(176,17,'','吕梁','',1,1),(177,17,'','朔州','',1,1),(178,17,'','忻州','',1,1),(179,17,'','阳泉','',1,1),(180,17,'','运城','',1,1),(181,18,'','石家庄','',1,1),(182,18,'','保定','',1,1),(183,18,'','沧州','',1,1),(184,18,'','承德','',1,1),(185,18,'','邯郸','',1,1),(186,18,'','衡水','',1,1),(187,18,'','廊坊','',1,1),(188,18,'','秦皇岛','',1,1),(189,18,'','唐山','',1,1),(190,18,'','邢台','',1,1),(191,18,'','张家口','',1,1),(192,19,'','哈尔滨','',1,1),(193,19,'','大庆','',1,1),(194,19,'','大兴安岭地区','',1,1),(195,19,'','鹤岗','',1,1),(196,19,'','黑河','',1,1),(197,19,'','佳木斯','',1,1),(198,19,'','鸡西','',1,1),(199,19,'','牡丹江','',1,1),(200,19,'','齐齐哈尔','',1,1),(201,19,'','七台河','',1,1),(202,19,'','双鸭山','',1,1),(203,19,'','绥化','',1,1),(204,19,'','伊春','',1,1),(205,20,'','长春','',1,1),(206,20,'','白城','',1,1),(207,20,'','白山','',1,1),(208,20,'','吉林','',1,1),(209,20,'','辽源','',1,1),(210,20,'','四平','',1,1),(211,20,'','松原','',1,1),(212,20,'','通化','',1,1),(213,20,'','延边朝鲜族自治州','',1,1),(214,21,'','沈阳','',1,1),(215,21,'','鞍山','',1,1),(216,21,'','本溪','',1,1),(217,21,'','朝阳','',1,1),(218,21,'','大连','',1,1),(219,21,'','丹东','',1,1),(220,21,'','抚顺','',1,1),(221,21,'','阜新','',1,1),(222,21,'','葫芦岛','',1,1),(223,21,'','锦州','',1,1),(224,21,'','辽阳','',1,1),(225,21,'','盘锦','',1,1),(226,21,'','铁岭','',1,1),(227,21,'','营口','',1,1),(228,22,'','海口','',1,1),(229,22,'','三亚','',1,1),(230,22,'','省直辖行政单位','',1,1),(231,23,'','南宁','',1,1),(232,23,'','百色','',1,1),(233,23,'','北海','',1,1),(234,23,'','崇左','',1,1),(235,23,'','防城港','',1,1),(236,23,'','贵港','',1,1),(237,23,'','桂林','',1,1),(238,23,'','河池','',1,1),(239,23,'','贺州','',1,1),(240,23,'','来宾','',1,1),(241,23,'','柳州','',1,1),(242,23,'','钦州','',1,1),(243,23,'','梧州','',1,1),(244,23,'','玉林','',1,1),(245,24,'','广州','',1,1),(246,24,'','潮州','',1,1),(247,24,'','东莞','',1,1),(248,24,'','佛山','',1,1),(249,24,'','河源','',1,1),(250,24,'','惠州','',1,1),(251,24,'','江门','',1,1),(252,24,'','揭阳','',1,1),(253,24,'','茂名','',1,1),(254,24,'','梅州','',1,1),(255,24,'','清远','',1,1),(256,24,'','汕头','',1,1),(257,24,'','汕尾','',1,1),(258,24,'','韶关','',1,1),(259,24,'','深圳','',1,1),(260,24,'','阳江','',1,1),(261,24,'','云浮','',1,1),(262,24,'','湛江','',1,1),(263,24,'','肇庆','',1,1),(264,24,'','中山','',1,1),(265,24,'','珠海','',1,1),(266,25,'','拉萨','',1,1),(267,25,'','阿里地区','',1,1),(268,25,'','昌都地区','',1,1),(269,25,'','林芝地区','',1,1),(270,25,'','那曲地区','',1,1),(271,25,'','日喀则地区','',1,1),(272,25,'','山南地区','',1,1),(273,26,'','贵阳','',1,1),(274,26,'','安顺','',1,1),(275,26,'','毕节地区','',1,1),(276,26,'','六盘水','',1,1),(277,26,'','黔东南苗族侗族自治州','',1,1),(278,26,'','黔南布依族苗族自治州','',1,1),(279,26,'','黔西南布依族苗族自治州','',1,1),(280,26,'','铜仁地区','',1,1),(281,26,'','遵义','',1,1),(282,27,'','成都','',1,1),(283,27,'','阿坝州','',1,1),(284,27,'','巴中','',1,1),(285,27,'','达州','',1,1),(286,27,'','德阳','',1,1),(287,27,'','甘孜藏族自治州','',1,1),(288,27,'','广安','',1,1),(289,27,'','广元','',1,1),(290,27,'','乐山','',1,1),(291,27,'','凉山州','',1,1),(292,27,'','泸州','',1,1),(293,27,'','眉山','',1,1),(294,27,'','绵阳','',1,1),(295,27,'','南充','',1,1),(296,27,'','内江','',1,1),(297,27,'','攀枝花','',1,1),(298,27,'','遂宁','',1,1),(299,27,'','雅安','',1,1),(300,27,'','宜宾','',1,1),(301,27,'451,452','自贡','',1,1),(302,27,'','资阳','',1,1),(303,28,'','昆明','',1,1),(304,28,'','保山','',1,1),(305,28,'','楚雄彝族自治州','',1,1),(306,28,'','大理白族自治州','',1,1),(307,28,'','德宏傣族景颇族自治州','',1,1),(308,28,'','迪庆藏族自治州','',1,1),(309,28,'','红河哈尼族自治州','',1,1),(310,28,'','丽江','',1,1),(311,28,'','临沧','',1,1),(312,28,'','怒江傈僳族自治州','',1,1),(313,28,'','普洱','',1,1),(314,28,'','曲靖','',1,1),(315,28,'','文山壮族苗族自治州','',1,1),(316,28,'','西双版纳傣族自治州','',1,1),(317,28,'','玉溪','',1,1),(318,28,'','昭通','',1,1),(319,29,'','乌鲁木齐','',1,1),(320,29,'','阿克苏','',1,1),(321,29,'','阿勒泰地区','',1,1),(322,29,'','巴音郭楞蒙古自治州','',1,1),(323,29,'','博尔塔拉蒙古自治州','',1,1),(324,29,'','昌吉回族自治州','',1,1),(325,29,'','哈密地区','',1,1),(326,29,'','和田地区','',1,1),(327,29,'','喀什地区','',1,1),(328,29,'','克拉玛依','',1,1),(329,29,'','克孜勒苏柯尔克孜自治州','',1,1),(330,29,'','塔城地区','',1,1),(331,29,'','吐鲁番','',1,1),(332,29,'','伊犁州','',1,1),(333,29,'','自治区直辖县级行政单位','',1,1),(334,30,'','银川','',1,1),(335,30,'','固原','',1,1),(336,30,'','石嘴山','',1,1),(337,30,'','吴忠','',1,1),(338,30,'','中卫','',1,1),(339,31,'','西宁','',1,1),(340,31,'','果洛州','',1,1),(341,31,'','海北州','',1,1),(342,31,'','海东地区','',1,1),(343,31,'','海南州','',1,1),(344,31,'','海西州','',1,1),(345,31,'','黄南州','',1,1),(346,31,'','玉树州','',1,1),(347,32,'','兰州','',1,1),(348,32,'','白银','',1,1),(349,32,'','定西','',1,1),(350,32,'','甘南州','',1,1),(351,32,'','陇南','',1,1),(352,32,'','嘉峪关','',1,1),(353,32,'','金昌','',1,1),(354,32,'','酒泉','',1,1),(355,32,'','临夏州','',1,1),(356,32,'','平凉','',1,1),(357,32,'','庆阳','',1,1),(358,32,'','天水','',1,1),(359,32,'','武威','',1,1),(360,32,'','张掖','',1,1),(361,33,'','西安','',1,1),(362,33,'','安康','',1,1),(363,33,'','宝鸡','',1,1),(364,33,'','汉中','',1,1),(365,33,'','商洛','',1,1),(366,33,'','铜川','',1,1),(367,33,'','渭南','',1,1),(368,33,'','咸阳','',1,1),(369,33,'','延安','',1,1),(370,33,'','榆林','',1,1),(371,5,'','北碚','',1,1),(372,5,'','万盛','',1,1),(373,5,'','渝北','',1,1),(374,5,'','巴南','',1,1),(375,5,'','万州','',1,1),(376,5,'','涪陵','',1,1),(377,5,'','黔江','',1,1),(378,5,'','长寿','',1,1),(379,5,'','綦江','',1,1),(380,5,'','潼南','',1,1),(381,5,'','荣昌','',1,1),(382,5,'','璧山','',1,1),(383,5,'','大足','',1,1),(384,5,'','铜梁','',1,1),(385,5,'','梁平','',1,1),(386,5,'','城口','',1,1),(387,5,'','垫江','',1,1),(388,5,'','武隆','',1,1),(389,5,'','丰都','',1,1),(390,5,'','奉节','',1,1),(391,5,'','开县','',1,1),(392,5,'','云阳','',1,1),(393,5,'','忠县','',1,1),(394,5,'','巫溪','',1,1),(395,5,'','巫山','',1,1),(396,5,'','石柱','',1,1),(397,5,'','秀山','',1,1),(398,5,'','酉阳','',1,1),(399,5,'','彭水','',1,1),(400,5,'','永川','',1,1),(401,5,'','合川','',1,1),(402,5,'','江津','',1,1),(403,5,'','南川','',1,1),(404,1,'','朝阳','',1,1),(405,1,'','丰台','',1,1),(406,1,'','石景山','',1,1),(407,1,'','海淀','',1,1),(408,1,'','门头沟','',1,1),(409,1,'','房山','',1,1),(410,1,'','通州','',1,1),(411,1,'','顺义','',1,1),(412,1,'','昌平','',1,1),(413,1,'','大兴','',1,1),(414,1,'','怀柔','',1,1),(415,1,'','平谷','',1,1),(416,1,'','密云','',1,1),(417,1,'','延庆','',1,1),(418,3,'','塘沽','',1,1),(419,3,'','汉沽','',1,1),(420,3,'','大港','',1,1),(421,3,'','东丽','',1,1),(422,3,'','西青','',1,1),(423,3,'','北辰','',1,1),(424,3,'','津南','',1,1),(425,3,'','武清','',1,1),(426,3,'','宝坻','',1,1),(427,3,'','静海','',1,1),(428,3,'','宁河','',1,1),(429,3,'','蓟县','',1,1),(430,2,'','徐家','',1,1),(431,2,'','宝山','',1,1),(432,2,'','闵行','',1,1),(433,2,'','嘉定','',1,1),(434,2,'','浦东新','',1,1),(435,2,'','松江','',1,1),(436,2,'','金山','',1,1),(437,2,'','青浦','',1,1),(438,2,'','南汇','',1,1),(439,2,'','奉贤','',1,1),(440,2,'','崇明','',1,1),(441,4,'','番禺','',1,1),(442,4,'','增城','',1,1),(443,4,'','从化','',1,1),(444,4,'','花都','',1,1),(445,170,'','清徐','',1,1),(446,170,'','娄烦','',1,1),(447,170,'','阳曲','',1,1),(448,170,'','太原南郊','',1,1),(449,170,'','太原古交','',1,1),(450,170,'','太原北郊','',1,1),(451,301,'','荣县','',1,1),(452,301,'','富顺','',1,1);
/*!40000 ALTER TABLE `oun_citycat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_dds`
--

LOCK TABLES `oun_dds` WRITE;
/*!40000 ALTER TABLE `oun_dds` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_dds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_ddscarts`
--

LOCK TABLES `oun_ddscarts` WRITE;
/*!40000 ALTER TABLE `oun_ddscarts` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_ddscarts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_filter`
--

LOCK TABLES `oun_filter` WRITE;
/*!40000 ALTER TABLE `oun_filter` DISABLE KEYS */;
INSERT INTO `oun_filter` VALUES (1,'','从独裁到民主=##\r\n法L=##        \r\n法轮功=##\r\n诺贝尔和平奖=##\r\n示威=##    \r\n台湾轮盘=##    \r\n游行=##\r\n77元房客=##       \r\n钓鱼岛事件=##    \r\n反日游行=##       \r\n月租77元=##    \r\n中国太子党=##     \r\n撞船事件=##\r\n习!近平=##    \r\n刁近平=##    \r\n温家室=##   \r\n贺!国强=##    \r\n王!岐山=## \r\n钱云会案=##\r\n办理警察证=##\r\n办&证=##\r\n办-证=##   \r\n考试答案=##    \r\n公务员考试=##\r\n枪手=##    \r\n身份证复印件生成器=##\r\n身份证号码生成器=##   \r\n售考前答案=##    \r\n助◆考=##    \r\n助考=##      \r\n替考=##\r\n电棍=##   \r\n电击棍=##   \r\n电警棒=##    \r\n防身用品=##   \r\n仿真枪=##    \r\n仿真槍=##    \r\n弓弩=##   \r\n警棒=##    \r\n手铐=##   \r\n手拷=##\r\n翻（HX）墙=##    \r\n翻墙=##   \r\n火凤凰=##    \r\n凸墙=##    \r\n无界=##    \r\n无界浏览器=##    \r\n逍遥游=##     \r\n自由门=##\r\nGHB水=##   \r\nK粉=##  \r\n阿普唑仑=##   \r\n安定片=##   \r\n安眠药=##   \r\n安眠藥=##    \r\n白冰=##   \r\n苯基丙酮=##    \r\n冰毒=##   \r\n冰糖=##   \r\n春药=##  \r\n催情药=##  \r\n胡椒基丙酮=##   \r\n胡椒醛=##   \r\n化工原料=##   \r\n化学冰=##   \r\n黄冰=##    \r\n甲基苯丙胺=##   \r\n甲醚高锰酸钾=##  \r\n麻古=##\r\n麻黄碱=##  \r\n麻黄素=##   \r\n迷幻药=##  \r\n迷魂=##  \r\n迷魂藥=##  \r\n缅古=##   \r\n缅果=##  \r\n曲马多=##   \r\n三唑仑=##   \r\n三唑侖=##  \r\n硝甲西泮=## \r\n牙签=##  \r\n盐酸氯胺酮=##   \r\n盐酸羟亚胺=##   \r\n摇头丸=##   \r\n乙醚=##   \r\n乙烯甲醇=##   \r\n植物冰=##   \r\n左旋麻黄素=##\r\nAV女优=##   \r\nAV片=##  \r\nIII级片=##   \r\n成人電影=##  \r\n成人社區=##   \r\n黄色电影=##  \r\n黄色小说=##   \r\n激情短片=##  \r\n一夜情交友=## \r\n上门按摩=##   \r\n色情自拍=##   \r\n诱惑视频=##  \r\n人体写真=##   \r\n迷歼药=##  \r\n54式手枪=##   \r\n5.5MM狗粮=##   \r\n64式手枪=##   \r\n狗粮4.5MM=##   \r\n獵槍=##   \r\n火药制作=##  \r\n麻醉槍=##  \r\n气枪=##  \r\n气槍=##   \r\n汽狗=##\r\n手枪=##  \r\n手木仑=##   \r\n手槍=##   \r\n子弹=##\r\n1元硬币=##   \r\nS F传奇=##   \r\n出售银行卡=##   \r\n代开发票=##   \r\n發票=##   \r\n风云私服=##   \r\n黑车=##   \r\n假钞=##   \r\n假币=##  \r\n手机卡复制器=##   \r\n套牌车=##   \r\n一元硬币=##   \r\n指纹膜=##   \r\n走私车=##\r\n跟踪定位器=##    \r\n监听王=##   \r\n考试作弊器=##   \r\n窃听器=##   \r\n瑞士军刀=##   \r\n手机切听器=##  \r\n透视镜=##  \r\n指纹套=##\r\n赌博机=##   \r\n赌球=##   \r\n合彩开奖=##  \r\n老虎机=##   \r\n六合彩=##   \r\n六和彩=##','',1);
/*!40000 ALTER TABLE `oun_filter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_inducat`
--

LOCK TABLES `oun_inducat` WRITE;
/*!40000 ALTER TABLE `oun_inducat` DISABLE KEYS */;
INSERT INTO `oun_inducat` VALUES (1,0,'17,18,19,20,21,22,23,24,25,26,27,28','旅游','',1,1),(3,0,'','娱乐','',1,1),(4,0,'','休闲','',1,1),(5,0,'','购物','',1,1),(6,0,'','机械设备','',1,1),(7,0,'','通用零部件','',1,1),(8,0,'','日常服务','',1,1),(9,0,'','纺织','',1,1),(10,0,'','皮革','',1,1),(11,0,'','服装','',1,1),(12,0,'','鞋帽','',1,1),(13,0,'','家具','',1,1),(14,0,'','生活用品','',1,1),(15,0,'','食品','',1,1),(30,29,'','软件','',1,1),(17,1,'','宾馆','',1,1),(18,1,'','餐饮','',1,1),(19,1,'','休闲娱乐','',1,1),(20,1,'','浴场','',1,1),(21,1,'','体育','',1,1),(22,1,'','休闲运动','',1,1),(23,1,'','宠物、花鸟','',1,1),(24,1,'','文化艺术','',1,1),(25,1,'','购物','',1,1),(26,1,'','体育','',1,1),(27,1,'','文娱用品','',1,1),(31,29,'','硬件','',1,1),(28,1,'','厨房设备用品','',1,1),(29,0,',','IT','',1,1);
/*!40000 ALTER TABLE `oun_inducat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_links`
--

LOCK TABLES `oun_links` WRITE;
/*!40000 ALTER TABLE `oun_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_messages`
--

LOCK TABLES `oun_messages` WRITE;
/*!40000 ALTER TABLE `oun_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_messagesre`
--

LOCK TABLES `oun_messagesre` WRITE;
/*!40000 ALTER TABLE `oun_messagesre` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_messagesre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_messagesread`
--

LOCK TABLES `oun_messagesread` WRITE;
/*!40000 ALTER TABLE `oun_messagesread` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_messagesread` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_nav`
--

LOCK TABLES `oun_nav` WRITE;
/*!40000 ALTER TABLE `oun_nav` DISABLE KEYS */;
INSERT INTO `oun_nav` VALUES (1734,1,'新闻评论',0,0,0,'acomms.php','',0,1),(1733,1,'简历申请',0,0,0,'jobadd.php','',0,1),(1740,1,'RSS聚合频道',0,0,0,'map.php','',0,1),(1741,1,'网上调查',0,0,0,'votes.php','',0,1),(1716,1,'商家列表',0,0,0,'pravail.php','',0,1),(1783,0,'服务网络',0,5,0,'sernet.php','',0,1),(1713,0,'模版文档',0,0,0,'articles.php?id=11','',1476,1),(1712,0,'安装方法',0,0,0,'articles.php?id=10','',1476,1),(1474,1,'网站申请注册',0,0,0,'webreg.php','',0,1),(1462,0,'关于我们',0,1,0,'about.php','',0,1),(1738,1,'服务网络',0,0,0,'sernet.php','',0,1),(1742,2,'关于我们',0,0,0,'','1349698160911001218.jpg',0,1),(1737,1,'品牌列表',0,0,0,'brands.php','',0,1),(22,0,'用户网站',0,3,0,'vip.php','',0,1),(1476,0,'新闻列表',0,2,0,'articles.php','',0,1),(21,0,'客户留言',0,6,0,'support.php','',0,1),(1735,1,'站内搜索',0,0,0,'search.php','',0,1),(1739,1,'友情连接',0,0,0,'links.php','',0,1),(1743,2,'配送方式',0,0,0,'','1349698181600122669.jpg',0,1),(1744,2,'付款方式',0,0,0,'','1349698191667231397.jpg',0,1),(1745,2,'我的订单',0,0,0,'','1349698202585040821.jpg',0,1),(1746,2,'售后服务',0,0,0,'','1349698216585761927.jpg',0,1),(1747,2,'需要帮助',0,0,0,'','1349698227281947311.jpg',0,1),(1748,2,'公司简介',0,0,0,'about.php','',1742,1),(1749,2,'联系我们',0,0,0,'','',1742,1),(1750,2,'地理位置',0,0,0,'','',1742,1),(1751,2,'加盟合作',0,0,0,'','',1742,1),(1752,2,'配送时间',0,0,0,'','',1743,1),(1753,2,'送货上门',0,0,0,'','',1743,1),(1754,2,'自提货',0,0,0,'','',1743,1),(1755,2,'物流快递',0,0,0,'','',1743,1),(1756,2,'如何付款',0,0,0,'','',1744,1),(1757,2,'网上在线支付',0,0,0,'','',1744,1),(1758,2,'银行汇款',0,0,0,'','',1744,1),(1759,2,'查看订单',0,0,0,'user.php?o=ding','',1745,1),(1760,2,'如何订货',0,0,0,'','',1745,1),(1761,2,'服务承诺',0,0,0,'','',1746,1),(1762,2,'退换政策',0,0,0,'','',1746,1),(1763,2,'退换流程',0,0,0,'','',1746,1),(1764,2,'售后答疑',0,0,0,'','',1746,1),(1765,2,'忘记密码',0,0,0,'user.php?o=f','',1747,1),(1766,2,'常见问题',0,0,0,'','',1747,1),(1767,2,'意见反馈',0,0,1,'support.php','',1747,1),(1776,0,'首页',0,0,0,'index.php','',0,1);
/*!40000 ALTER TABLE `oun_nav` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_prattcat`
--

LOCK TABLES `oun_prattcat` WRITE;
/*!40000 ALTER TABLE `oun_prattcat` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_prattcat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_prattri`
--

LOCK TABLES `oun_prattri` WRITE;
/*!40000 ALTER TABLE `oun_prattri` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_prattri` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_prattrival`
--

LOCK TABLES `oun_prattrival` WRITE;
/*!40000 ALTER TABLE `oun_prattrival` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_prattrival` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail`
--

LOCK TABLES `oun_pravail` WRITE;
/*!40000 ALTER TABLE `oun_pravail` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_pravail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail_arti_file`
--

LOCK TABLES `oun_pravail_arti_file` WRITE;
/*!40000 ALTER TABLE `oun_pravail_arti_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_pravail_arti_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail_article`
--

LOCK TABLES `oun_pravail_article` WRITE;
/*!40000 ALTER TABLE `oun_pravail_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_pravail_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail_artitxt`
--

LOCK TABLES `oun_pravail_artitxt` WRITE;
/*!40000 ALTER TABLE `oun_pravail_artitxt` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_pravail_artitxt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail_prattrival`
--

LOCK TABLES `oun_pravail_prattrival` WRITE;
/*!40000 ALTER TABLE `oun_pravail_prattrival` DISABLE KEYS */;
INSERT INTO `oun_pravail_prattrival` VALUES (8,21,2,'肉色',268),(7,20,2,'50KG',268),(6,19,2,'人形',268),(9,22,2,'26CM',268),(10,23,2,'160CM',268),(11,20,3,'sdf',1),(12,21,3,'ge',1),(13,22,3,'efds',1),(14,23,3,'grg',1);
/*!40000 ALTER TABLE `oun_pravail_prattrival` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail_product`
--

LOCK TABLES `oun_pravail_product` WRITE;
/*!40000 ALTER TABLE `oun_pravail_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_pravail_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail_product_comms`
--

LOCK TABLES `oun_pravail_product_comms` WRITE;
/*!40000 ALTER TABLE `oun_pravail_product_comms` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_pravail_product_comms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail_product_file`
--

LOCK TABLES `oun_pravail_product_file` WRITE;
/*!40000 ALTER TABLE `oun_pravail_product_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_pravail_product_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail_productcat`
--

LOCK TABLES `oun_pravail_productcat` WRITE;
/*!40000 ALTER TABLE `oun_pravail_productcat` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_pravail_productcat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_pravail_producttxt`
--

LOCK TABLES `oun_pravail_producttxt` WRITE;
/*!40000 ALTER TABLE `oun_pravail_producttxt` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_pravail_producttxt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_price_history`
--

LOCK TABLES `oun_price_history` WRITE;
/*!40000 ALTER TABLE `oun_price_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_price_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_probrand`
--

LOCK TABLES `oun_probrand` WRITE;
/*!40000 ALTER TABLE `oun_probrand` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_probrand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_probrand_procat`
--

LOCK TABLES `oun_probrand_procat` WRITE;
/*!40000 ALTER TABLE `oun_probrand_procat` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_probrand_procat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_product`
--

LOCK TABLES `oun_product` WRITE;
/*!40000 ALTER TABLE `oun_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_product_comms`
--

LOCK TABLES `oun_product_comms` WRITE;
/*!40000 ALTER TABLE `oun_product_comms` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_product_comms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_product_file`
--

LOCK TABLES `oun_product_file` WRITE;
/*!40000 ALTER TABLE `oun_product_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_product_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_productcat`
--

LOCK TABLES `oun_productcat` WRITE;
/*!40000 ALTER TABLE `oun_productcat` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_productcat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_producttxt`
--

LOCK TABLES `oun_producttxt` WRITE;
/*!40000 ALTER TABLE `oun_producttxt` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_producttxt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_prtopra`
--

LOCK TABLES `oun_prtopra` WRITE;
/*!40000 ALTER TABLE `oun_prtopra` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_prtopra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_qq`
--

LOCK TABLES `oun_qq` WRITE;
/*!40000 ALTER TABLE `oun_qq` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_qq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_sernet`
--

LOCK TABLES `oun_sernet` WRITE;
/*!40000 ALTER TABLE `oun_sernet` DISABLE KEYS */;
INSERT INTO `oun_sernet` VALUES (12,'heilongjiang','黑龙江','黑龙江','',1,1),(11,'jilin','吉林','吉林','',1,1),(10,'liaoning','辽宁','辽宁','',1,1),(9,'shanxi','山西','山西','',1,1),(8,'ningxia','宁夏','宁夏','',1,1),(7,'neimenggu','内蒙古','内蒙古','',1,1),(6,'gansu','甘肃','甘肃','',1,1),(5,'qinghai','青海','青海','',1,1),(4,'xizang','西藏','西藏','',1,1),(3,'xinjiang','新疆','新疆','',1,1),(2,'tianjin','天津','天津','',1,1),(1,'beijing','北京','北京-暂无经销商','',1,1),(13,'hebei','河北','河北','',1,1),(14,'shandong','山东','山东','',1,1),(15,'henan','河南','河南','',1,1),(16,'shannxi','陕西','陕西','',1,1),(17,'sichuan','四川','四川','',1,1),(18,'chongqing','重庆','行业之星工作室 VIP QQ:16953292,Tel:13399853319','about.php',1,1),(19,'hubei','湖北','湖北','',1,1),(20,'anhui','安徽','安徽','',1,1),(21,'jiangsu','江苏','江苏','',1,1),(22,'shanghai','上海','上海','',1,1),(23,'zhejiang','浙江','浙江','',1,1),(24,'fujian','福建','福建','',1,1),(25,'taiwan','台湾','台湾','',0,1),(26,'jiangxi','江西','江西','',1,1),(27,'hunan','湖南','湖南','',1,1),(28,'guizhou','贵州','贵州','',1,1),(29,'guangxi','广西','广西','',1,1),(30,'guangdong','广东','广东','',1,1),(31,'xianggang','香港','香港','',1,1),(32,'hainan','海南','海南','',1,1),(33,'yunnan','云南','云南','',1,1),(97,'guangdong','广东','','',1,268),(96,'guangxi','广西','','',1,268),(95,'guizhou','贵州','','',1,268),(94,'hunan','湖南','','',1,268),(93,'jiangxi','江西','','',1,268),(92,'taiwan','台湾','','',1,268),(91,'fujian','福建','','',1,268),(90,'zhejiang','浙江','','',1,268),(89,'shanghai','上海','','',1,268),(88,'jiangsu','江苏','','',1,268),(87,'anhui','安徽','','',1,268),(86,'hubei','湖北','','',1,268),(85,'chongqing','重庆','','',1,268),(84,'sichuan','四川','','',1,268),(83,'shannxi','陕西','','',1,268),(82,'henan','河南','','',1,268),(81,'shandong','山东','','',1,268),(80,'hebei','河北','','',1,268),(79,'heilongjiang','黑龙江','','',1,268),(78,'jilin','吉林','','',1,268),(77,'liaoning','辽宁','','',1,268),(76,'shanxi','山西','','',1,268),(75,'ningxia','宁夏','','',1,268),(74,'neimenggu','内蒙古','','',1,268),(73,'gansu','甘肃','','',1,268),(72,'qinghai','青海','','',1,268),(71,'xizang','西藏','','',1,268),(70,'xinjiang','新疆','','',1,268),(69,'tianjin','天津','','',1,268),(68,'beijing','北京','北京哈哈','',1,268),(98,'xianggang','香港','','',1,268),(99,'hainan','海南','','',1,268),(100,'yunnan','云南','','',1,268);
/*!40000 ALTER TABLE `oun_sernet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_sessions`
--

LOCK TABLES `oun_sessions` WRITE;
/*!40000 ALTER TABLE `oun_sessions` DISABLE KEYS */;
INSERT INTO `oun_sessions` VALUES ('31993ce366618ab98bb93aa21c440f1c',1368840871,1,1,'127.0.0.1','',0);
/*!40000 ALTER TABLE `oun_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_sessions_data`
--

LOCK TABLES `oun_sessions_data` WRITE;
/*!40000 ALTER TABLE `oun_sessions_data` DISABLE KEYS */;
INSERT INTO `oun_sessions_data` VALUES ('31993ce366618ab98bb93aa21c440f1c',1368840871,'a:14:{s:7:\"user_id\";s:1:\"1\";s:9:\"user_name\";s:5:\"admin\";s:3:\"sex\";s:1:\"m\";s:8:\"ifmanger\";s:1:\"1\";s:6:\"avatar\";s:1:\"1\";s:12:\"aaction_list\";s:3:\"all\";s:5:\"vCode\";s:3:\"L74\";s:8:\"auser_id\";s:1:\"1\";s:10:\"auser_name\";s:5:\"admin\";s:16:\"aarticlecat_list\";s:0:\"\";s:6:\"apraid\";s:1:\"5\";s:10:\"domain_url\";s:24:\"http://www.test.com/aaa/\";s:9:\"domain_id\";s:1:\"1\";s:14:\"domain_user_id\";s:1:\"1\";}');
/*!40000 ALTER TABLE `oun_sessions_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_sesspro`
--

LOCK TABLES `oun_sesspro` WRITE;
/*!40000 ALTER TABLE `oun_sesspro` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_sesspro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_support`
--

LOCK TABLES `oun_support` WRITE;
/*!40000 ALTER TABLE `oun_support` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_support` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_support_re`
--

LOCK TABLES `oun_support_re` WRITE;
/*!40000 ALTER TABLE `oun_support_re` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_support_re` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_sysconfig`
--

LOCK TABLES `oun_sysconfig` WRITE;
/*!40000 ALTER TABLE `oun_sysconfig` DISABLE KEYS */;
INSERT INTO `oun_sysconfig` VALUES (1,1,'admin','www.test.com','cache_time[|]0{|}logo_w[|]190{|}logo_h[|]61{|}big_thumb_w[|]300{|}big_thumb_h[|]225{|}min_thumb_w[|]120{|}min_thumb_h[|]90{|}mis_thumb_w[|]60{|}mis_thumb_h[|]54{|}nav_w[|]60{|}nav_h[|]50{|}rewrite[|]1{|}article[|]0{|}reg_support[|]1{|}support[|]0{|}links[|]0{|}footer_title[|]版权所有 行业之星{|}icp[|]ICP备12001993号{|}sub_themes[|]gz500{|}sour_scid[|]{|}tongji[|]{|}title[|]开源免费多用户自助建站系统{|}keywords[|]重庆,行业之星,PHP开源,全站系统,行业之星程序,全站CMS,php+mysql开源免费{|}description[|]行业之星程序是由老兵工作室提供的,php开源免费多用户自助建站系统.{|}shop_name[|]行业之星工作室{|}contact[|]许永{|}phone[|]{|}fax[|]{|}tel[|]13399853319{|}zip[|]400060{|}address[|]重庆市南岸区南坪南城大道249号中富大厦{|}email[|]xy58@qq.com{|}msn[|]xufyong@gmail.com{|}qq[|]16953292{|}','one|notices:1,,,公告,;descs:1,,,公司简介,;articat:1,,,新闻分类,;productcat:1,,,商品分类,;vote:1,,,推荐调查,;sesspro:1,,,最近访问商品,;]two|keytj:1,8,,推荐关键词,;vip:1,8,,VIP客户,;articles:1,5,,新闻列表,;articles_top:1,3,,置顶新闻,;articles_focus:1,7,,焦点新闻,;products:1,25,,商品列表,;products_top:1,5,,特价促销,;products_special:1,5,,畅销商品,;votes:1,6,,调查列表,;links:1,10,,友情连接列表,;users:1,10,,新注册用户,;]three|','行业之星','<p style=\"background-color:#ffffff;text-indent:0px;color:#000000;\">\r\n	&nbsp;\r\n</p>','<p>\r\n	&nbsp;\r\n</p>\r\n<p>\r\n	&nbsp;\r\n</p>','1348466255461504470.png','','1329191135062787458.png',5,30,'simple',0,2,0,0,0);
/*!40000 ALTER TABLE `oun_sysconfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_sysconfigfast`
--

LOCK TABLES `oun_sysconfigfast` WRITE;
/*!40000 ALTER TABLE `oun_sysconfigfast` DISABLE KEYS */;
INSERT INTO `oun_sysconfigfast` VALUES (1,'admin','www.test.com',2);
/*!40000 ALTER TABLE `oun_sysconfigfast` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_sysnotice`
--

LOCK TABLES `oun_sysnotice` WRITE;
/*!40000 ALTER TABLE `oun_sysnotice` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_sysnotice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_syssmtp`
--

LOCK TABLES `oun_syssmtp` WRITE;
/*!40000 ALTER TABLE `oun_syssmtp` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_syssmtp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_systhemes`
--

LOCK TABLES `oun_systhemes` WRITE;
/*!40000 ALTER TABLE `oun_systhemes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_systhemes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_tj`
--

LOCK TABLES `oun_tj` WRITE;
/*!40000 ALTER TABLE `oun_tj` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_tj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_tjcat`
--

LOCK TABLES `oun_tjcat` WRITE;
/*!40000 ALTER TABLE `oun_tjcat` DISABLE KEYS */;
INSERT INTO `oun_tjcat` VALUES (1,'FLASH轮播广告',684,274,5,1,1,1),(24,'企业荣誉',228,171,5,1,2,322),(27,'FLASH轮播广告',1005,240,5,1,1,324),(15,'FLASH新品推荐',248,252,5,1,2,1),(26,'企业荣誉',228,171,5,1,2,323),(25,'FLASH轮播广告',1005,240,5,1,1,323),(23,'FLASH轮播广告',1005,240,5,1,1,322),(28,'企业荣誉',228,171,5,1,2,324),(29,'FLASH轮播广告',1005,240,5,1,1,325),(30,'企业荣誉',228,171,5,1,2,325);
/*!40000 ALTER TABLE `oun_tjcat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_uaddrs`
--

LOCK TABLES `oun_uaddrs` WRITE;
/*!40000 ALTER TABLE `oun_uaddrs` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_uaddrs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_udetail`
--

LOCK TABLES `oun_udetail` WRITE;
/*!40000 ALTER TABLE `oun_udetail` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_udetail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_ufv`
--

LOCK TABLES `oun_ufv` WRITE;
/*!40000 ALTER TABLE `oun_ufv` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_ufv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_urlrecord`
--

LOCK TABLES `oun_urlrecord` WRITE;
/*!40000 ALTER TABLE `oun_urlrecord` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_urlrecord` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_userfindpass`
--

LOCK TABLES `oun_userfindpass` WRITE;
/*!40000 ALTER TABLE `oun_userfindpass` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_userfindpass` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_users`
--

LOCK TABLES `oun_users` WRITE;
/*!40000 ALTER TABLE `oun_users` DISABLE KEYS */;
INSERT INTO `oun_users` VALUES (1,0,'admin@osunit.com','admin','0.00',1,'ed8f012232ba1eb7e15f42e81178e17c',0,'0000-00-00',0,1368840329,'2013-05-18 01:29:25','127.0.0.1',365,'','','','','',0,1,1);
/*!40000 ALTER TABLE `oun_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_users_comms`
--

LOCK TABLES `oun_users_comms` WRITE;
/*!40000 ALTER TABLE `oun_users_comms` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_users_comms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_users_job`
--

LOCK TABLES `oun_users_job` WRITE;
/*!40000 ALTER TABLE `oun_users_job` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_users_job` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_userstype`
--

LOCK TABLES `oun_userstype` WRITE;
/*!40000 ALTER TABLE `oun_userstype` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_userstype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_usersverify`
--

LOCK TABLES `oun_usersverify` WRITE;
/*!40000 ALTER TABLE `oun_usersverify` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_usersverify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_vote_group`
--

LOCK TABLES `oun_vote_group` WRITE;
/*!40000 ALTER TABLE `oun_vote_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_vote_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_vote_ip`
--

LOCK TABLES `oun_vote_ip` WRITE;
/*!40000 ALTER TABLE `oun_vote_ip` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_vote_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_vote_item`
--

LOCK TABLES `oun_vote_item` WRITE;
/*!40000 ALTER TABLE `oun_vote_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_vote_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_vote_poll`
--

LOCK TABLES `oun_vote_poll` WRITE;
/*!40000 ALTER TABLE `oun_vote_poll` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_vote_poll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `oun_vote_title`
--

LOCK TABLES `oun_vote_title` WRITE;
/*!40000 ALTER TABLE `oun_vote_title` DISABLE KEYS */;
/*!40000 ALTER TABLE `oun_vote_title` ENABLE KEYS */;
UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

