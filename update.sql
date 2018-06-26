
--
-- �����
-- ��Ľṹ `tx_goods_category`
--

CREATE TABLE IF NOT EXISTS `tx_goods_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '����id',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '���ุid',
  `goods_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '������Ʒ��',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '״̬,1:����,0:������',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ɾ��ʱ��',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '����',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '��������',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '��������',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '����㼶��ϵ·��',
  `more` text COMMENT '��չ����',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goodsӦ�� ��Ʒ�����';


--
-- �����������÷���
-- ��Ľṹ `tx_goods_car_config_category`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_config_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '�������÷���id',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '�������÷�������',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '����',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goodsӦ�� �����������÷���';


--
-- ���������������
-- ��Ľṹ `tx_goods_car_config_items`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_config_items` (
  `config_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '����������id',
  `config_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '��������������',
  `cate_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '�������÷���id',		
  `config_input_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 �ֹ�¼�� 1���б���ѡ�� 2�����ı���',	
  `config_values` text NOT NULL DEFAULT '' COMMENT '��ѡֵ�б�',	
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '����',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '����������',
  PRIMARY KEY (`config_id`),
  KEY `cate_id` (`cate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goodsӦ�� ���������������';

--
-- ��������������ֵ��
-- ��Ľṹ `tx_goods_car_config_values`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_config_values` (
  `config_id` int(11) unsigned NOT NULL  COMMENT '����������id',
  `config_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '����������ֵ',
  `car_style_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '����������ֵ',
  UNIQUE KEY `g_car_unique_id`(`config_id`,`car_style_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='goodsӦ�� ��������������ֵ��';


--
-- ������������ģ���
-- ��Ľṹ `tx_goods_car_config_template`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_config_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '��������ģ��id',
  `tpl_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '������������ģ������',
  `tpl_detail` text  NOT NULL DEFAULT '' COMMENT '������������',
  `is_default` tinyint(1)  unsigned NOT NULL DEFAULT '0' COMMENT 'Ĭ��ģ��:0:�� 1����',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ɾ��ʱ��',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '����',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'ģ������',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goodsӦ�� ������������ģ���';



--
-- ��Ľṹ `tx_goods_brand`
--
CREATE TABLE IF NOT EXISTS `tx_goods_brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Ʒ��id',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Ʒ������',		
  `icon` varchar(255)  NOT NULL DEFAULT ''  COMMENT 'Ʒ��logo',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '����״̬,1:������,0:������',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '��ʾ״̬,1:��ʾ,0:����',
  `first_char` varchar(1)  NOT NULL DEFAULT '' COMMENT 'ƴ������ĸ��д',
  `serach_pinyin` varchar(100)  NOT NULL DEFAULT '' COMMENT 'ƴ��',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ɾ��ʱ��',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '����',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'Ʒ������',
  `more` text COMMENT '��չ����',
  PRIMARY KEY (`id`),
  KEY `g_first_char` (`first_char`),
  KEY `g_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goodsӦ�� ��ƷƷ�Ʊ�';

--
-- ��Ľṹ `tx_goods_car_series`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_series` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '��ϵid',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '��ϵ����',
  `brand_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ʒ��id',	
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '״̬,1:������,0:������',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ɾ��ʱ��',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '����',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '��ϵ����',
  `more` text COMMENT '��չ����',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goodsӦ�� ����ϵ�б�';

--
-- �������Ϳ�ʽ
-- ��Ľṹ `tx_goods_car_style`
--
CREATE TABLE IF NOT EXISTS `tx_goods_car_style` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '����id',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '��������',
  `brand_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ʒ��id',	
  `series_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '��ϵid',	
  `grade_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '���ȼ�id',	
  `example_img` varchar(255) NOT NULL DEFAULT '' COMMENT '��������ͼ',	
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '����,1:������,0:��',
  `is_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '�Ƿ��Ƽ�,1:��,0:��',
  `car_config_tpl_id` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '��������ģ��id',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ɾ��ʱ��',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '����',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '��������',
  `more` text COMMENT '��չ����',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='goodsӦ�� �������Ϳ�ʽ��';






CREATE TABLE IF NOT EXISTS `tp_goods_attribute` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '����id',
  `attr_name` varchar(60) NOT NULL DEFAULT '' COMMENT '��������',
  `type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '���Է���id',
  `attr_index` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0����Ҫ���� 1�ؼ��ּ��� 2��Χ����',
  `attr_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0Ψһ���� 1��ѡ���� 2��ѡ����',
  `attr_input_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT ' 0 �ֹ�¼�� 1���б���ѡ�� 2�����ı���',
  `attr_values` text NOT NULL COMMENT '��ѡֵ�б�',
  `order` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '��������',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tp_goods_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id����',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '��������',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;






http://172.18.19.52


txsite  admin123==


 mysql 8.0
 
 root  szdx!0000 









