
--
-- 分类表
-- 表的结构 `tx_goods_category`
--

CREATE TABLE IF NOT EXISTS `tx_goods_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类父id',
  `goods_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类商品数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:启用,0:不启用',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '分类描述',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '分类层级关系路径',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 商品分类表';


--
-- 汽车参数配置分类
-- 表的结构 `tx_goods_car_config_category`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_config_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '汽车配置分类id',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '汽车配置分类名称',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 汽车参数配置分类';


--
-- 汽车参数配置项表
-- 表的结构 `tx_goods_car_config_items`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_config_items` (
  `config_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '汽车配置项id',
  `config_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '汽车配置项名称',
  `cate_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '汽车配置分类id',		
  `config_input_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 手工录入 1从列表中选择 2多行文本框',	
  `config_values` text NOT NULL DEFAULT '' COMMENT '可选值列表',	
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '配置项描述',
  PRIMARY KEY (`config_id`),
  KEY `cate_id` (`cate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 汽车参数配置项表';

--
-- 汽车参数配置项值表
-- 表的结构 `tx_goods_car_config_values`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_config_values` (
  `config_id` int(11) unsigned NOT NULL  COMMENT '汽车配置项id',
  `config_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '汽车配置项值',
  `car_style_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '汽车配置项值',
  UNIQUE KEY `g_car_unique_id`(`config_id`,`car_style_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 汽车参数配置项值表';


--
-- 汽车参数配置模板表
-- 表的结构 `tx_goods_car_config_template`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_config_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '汽车配置模板id',
  `tpl_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '汽车参数配置模板名称',
  `tpl_detail` text  NOT NULL DEFAULT '' COMMENT '汽车配置详情',
  `is_default` tinyint(1)  unsigned NOT NULL DEFAULT '0' COMMENT '默认模板:0:否 1：是',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '模板描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 汽车参数配置模板表';



--
-- 表的结构 `tx_goods_brand`
--
CREATE TABLE IF NOT EXISTS `tx_goods_brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '品牌id',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌名称',		
  `icon` varchar(255)  NOT NULL DEFAULT ''  COMMENT '品牌logo',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '热门状态,1:是热门,0:是热门',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '显示状态,1:显示,0:隐藏',
  `first_char` varchar(1)  NOT NULL DEFAULT '' COMMENT '拼音首字母大写',
  `serach_pinyin` varchar(100)  NOT NULL DEFAULT '' COMMENT '拼音',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '品牌描述',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  KEY `g_first_char` (`first_char`),
  KEY `g_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 商品品牌表';

--
-- 表的结构 `tx_goods_car_series`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_series` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '车系id',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '车系名称',
  `brand_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '品牌id',	
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态,1:是热门,0:是热门',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '车系描述',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 汽车系列表';

--
-- 汽车车型款式
-- 表的结构 `tx_goods_car_style`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_style` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '车型id',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '车型名称',
  `brand_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '品牌id',	
  `series_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '车系id',	
  `grade_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '车等级id',	
  `example_img` varchar(255) NOT NULL DEFAULT '' COMMENT '车型样例图',	
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '热门,1:是热门,0:否',
  `is_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐,1:是,0:否',
  `car_config_tpl_id` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '参数配置模板id',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '车型描述',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 汽车车型款式表';






CREATE TABLE IF NOT EXISTS `tp_goods_attribute` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attr_name` varchar(60) NOT NULL DEFAULT '' COMMENT '属性名称',
  `type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '属性分类id',
  `attr_index` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不需要检索 1关键字检索 2范围检索',
  `attr_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0唯一属性 1单选属性 2复选属性',
  `attr_input_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT ' 0 手工录入 1从列表中选择 2多行文本框',
  `attr_values` text NOT NULL COMMENT '可选值列表',
  `order` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '属性排序',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tp_goods_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id自增',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '类型名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;






http://172.18.19.52


txsite  admin123==


 mysql 8.0
 
 root  szdx!0000 









