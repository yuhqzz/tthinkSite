
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
  `factory_price` decimal(10,2) DEFAULT '0.00' COMMENT '厂家指导价',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '车型描述',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  KEY `series_id`(`series_id`),
  KEY `brand_id`(`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 汽车车型款式表';


--
-- 汽车实体表
-- 表的结构 `tx_goods`
--
CREATE TABLE  IF NOT EXISTS `tx_goods` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `brand_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '品牌id',	
  `series_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '车系id',	
  `style_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '车型id',
  `grade_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '车等级id',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
  `comment_count` smallint(5) DEFAULT '0' COMMENT '商品评论数',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场售价',
  `shop_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '本店售价',
  `factory_price` decimal(10,2) DEFAULT '0.00' COMMENT '厂家指导价',
  `price_ladder` text COMMENT '价格阶梯',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '商品关键词',
  `goods_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '商品简单描述',
  `description` text COMMENT '商品详细描述',
  `original_img` varchar(255) NOT NULL DEFAULT '' COMMENT '商品上传原始图',
  `is_on_sale` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否上架',
  `on_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品上架时间',
  `list_order` smallint(4) unsigned NOT NULL DEFAULT '50' COMMENT '商品排序',
  `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `is_new` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否新品',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '是否热卖',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `last_update` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `sales_sum` int(11) DEFAULT '0' COMMENT '商品销量',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '属性模型id',
  PRIMARY KEY (`id`),
  KEY `cat_id` (`category_id`),
  KEY `last_update` (`last_update`),
  KEY `create_time` (`create_time`),
  KEY `brand_id` (`brand_id`),
  KEY `series_id` (`series_id`),
  KEY `grade_id` (`grade_id`),
  KEY `sort_order` (`list_order`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 汽车实体表';


--
-- 汽车实体表
-- 表的结构 `tx_goods_type`
--
CREATE TABLE IF NOT EXISTS`tx_goods_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id自增',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '类型名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 商品属性分类表';

--
-- 商品属性值表
-- 表的结构 `tx_goods_attr`
--
CREATE TABLE IF NOT EXISTS `tx_goods_attr` (
  `goods_attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品属性id自增',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `attr_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '属性id',
  `attr_value` text NOT NULL COMMENT '属性值',
  `attr_price` varchar(255) NOT NULL DEFAULT '' COMMENT '属性价格',
  PRIMARY KEY (`goods_attr_id`),
  KEY `goods_id` (`goods_id`),
  KEY `attr_id` (`attr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 商品属性值表';

--
-- 商品属性表
-- 表的结构 `tx_goods_attribute`
--
CREATE TABLE `tx_goods_attribute` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attr_name` varchar(60) NOT NULL DEFAULT '' COMMENT '属性名称',
  `type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '属性分类id',
  `attr_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0唯一属性 1单选属性 2复选属性',
  `attr_input_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT ' 0 手工录入 1从列表中选择',
  `attr_values` text NOT NULL COMMENT '可选值列表',
  `list_order` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '属性排序',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 商品属性表';

--
-- 商品图片表
-- 表的结构 `tx_goods_attribute`
--
CREATE TABLE `tx_goods_images` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片id 自增',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `name` varchar(500) NOT NULL DEFAULT '' COMMENT '图片名称',
  `image_url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
  PRIMARY KEY (`img_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 商品图片表';

--
-- 经销商表
-- 表的结构 `tx_goods_dealers`
--
CREATE TABLE `tx_goods_dealers` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id自增',
  `name` varchar(500) NOT NULL DEFAULT '' COMMENT '经销商名称',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '经销商地址',
  `telephone` varchar(255) NOT NULL DEFAULT '' COMMENT '经销商咨询电话',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 经销商表';

--
-- 经销商与车源关系表
-- 表的结构 `tx_goods_dealers_map`
--
CREATE TABLE `tx_goods_dealers_map` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id自增',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `dealers_id` varchar(255) NOT NULL DEFAULT '' COMMENT '经销商_id',
  `dealers_price` decimal(10,2) DEFAULT '0.00' COMMENT '裸车价',
  
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`),
  KEY `dealers_id` (`dealers_id`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goods应用 商品图片表';

ALTER TABLE `tx_recycle_bin`
ADD COLUMN `user_id`  int(11) NOT NULL DEFAULT 0 AFTER `name`;
