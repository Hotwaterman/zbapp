-- ----------------------------
-- Table structure for `fox_chatgpt_article`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_article`;
CREATE TABLE `fox_chatgpt_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `content` longtext,
  `weight` int(11) DEFAULT '100' COMMENT '越大越靠前',
  `views` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_article
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_book`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_book`;
CREATE TABLE `fox_chatgpt_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `weight` int(11) DEFAULT '100',
  `state` tinyint(1) DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_book
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_book_data`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_book_data`;
CREATE TABLE `fox_chatgpt_book_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `book_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `content` longtext,
  `embedding_title` longtext,
  `embedding_content` longtext,
  `weight` int(11) DEFAULT '100',
  `state` tinyint(1) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_book_data
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_card`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_card`;
CREATE TABLE `fox_chatgpt_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `batch_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `code` varchar(255) DEFAULT '0' COMMENT '卡密',
  `type` varchar(10) DEFAULT NULL,
  `val` int(11) DEFAULT '0',
  `bind_time` int(11) DEFAULT '0' COMMENT '使用时间',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_card
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_card_batch`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_card_batch`;
CREATE TABLE `fox_chatgpt_card_batch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `num` int(11) DEFAULT '0' COMMENT '数量',
  `type` varchar(10) DEFAULT NULL,
  `val` int(11) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_card_batch
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_commission_apply`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_commission_apply`;
CREATE TABLE `fox_chatgpt_commission_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0' COMMENT '用户表id',
  `level` int(11) DEFAULT '1',
  `pid` int(11) DEFAULT '0' COMMENT '上级user_id',
  `name` varchar(50) CHARACTER SET utf8 DEFAULT '' COMMENT '分销商姓名',
  `phone` varchar(20) CHARACTER SET utf8 DEFAULT '0' COMMENT '手机号',
  `idcard` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '身份证号',
  `invite_code` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '邀请码',
  `total_fee` int(11) DEFAULT '0' COMMENT '需支付金额',
  `platform` varchar(20) CHARACTER SET utf8 DEFAULT 'wxapp' COMMENT '申请来源wxapp/app',
  `pay_type` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `pay_time` int(11) DEFAULT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0未审核 1审核成功 2驳回',
  `reject_reason` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '驳回原因',
  `remark` text CHARACTER SET utf8 COMMENT '备注',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '0未删除1已删除',
  `audit_time` int(11) DEFAULT '0' COMMENT '审核时间',
  `create_time` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_commission_apply
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_commission_bill`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_commission_bill`;
CREATE TABLE `fox_chatgpt_commission_bill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` tinyint(1) DEFAULT '1' COMMENT '1收入 2提现 3退款 4提现退回',
  `money` int(11) DEFAULT NULL,
  `is_lock` tinyint(1) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_commission_bill
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_commission_withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_commission_withdraw`;
CREATE TABLE `fox_chatgpt_commission_withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `money` int(11) DEFAULT '0' COMMENT '提现金额（分）',
  `bank_name` varchar(100) DEFAULT NULL,
  `account_name` varchar(50) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `qrcode` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0待审核 1已打款 2已驳回',
  `audit_time` int(11) DEFAULT '0' COMMENT '审核时间',
  `reject_reason` varchar(255) DEFAULT NULL COMMENT '拒绝原因',
  `remark` varchar(255) DEFAULT NULL COMMENT '后台备注',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_commission_withdraw
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_cosplay_role`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_cosplay_role`;
CREATE TABLE `fox_chatgpt_cosplay_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `desc` varchar(1000) CHARACTER SET utf8mb4 DEFAULT NULL,
  `thumb` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `prompt` text CHARACTER SET utf8mb4,
  `hint` varchar(1000) CHARACTER SET utf8mb4 DEFAULT NULL,
  `welcome` varchar(1000) CHARACTER SET utf8mb4 DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `usages` int(11) DEFAULT '0',
  `fake_views` int(11) DEFAULT '0',
  `fake_usages` int(11) DEFAULT '0',
  `weight` int(11) DEFAULT '100',
  `book_open` tinyint(1) DEFAULT '0',
  `books` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `no_answer_action` varchar(10) CHARACTER SET utf8mb4 DEFAULT 'ai',
  `answer_content` text CHARACTER SET utf8mb4,
  `state` tinyint(1) DEFAULT '1',
  `is_delete` tinyint(1) DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_cosplay_role
-- ----------------------------
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('1', '1', '1', '面试官', null, null, '你是面试官，接下来请与我模拟面试过程。我希望你只作为面试官回答。不要一次写出所有的问题。我希望你只对我进行采访。问我问题，等待我的回答。不要写解释。像面试官一样一个一个问我，等我回答，并对我之前的回答评价打分。', '先输入你想面试的职位，然后回复AI的提问', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('2', '1', '1', '金牌销售', null, null, '你是销售员小明，希望你模拟销售员销售产品的过程，在我写出问题之后，你给我回复我问题的话术。', '先输入你想模拟销售的产品，然后与AI对话', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('3', '1', '2', '心理医生', null, null, '我想让你担任心理医生。我将为您提供一个寻求指导和建议的人，以管理他们的情绪、压力、焦虑和其他心理健康问题。您应该利用您的认知行为疗法、冥想技巧、正念练习和其他治疗方法的知识来制定个人可以实施的策略，以改善他们的整体健康状况。请你以心理医生治疗病人的风格引导我', '请描述你遇到的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('4', '1', '2', '营养师', null, null, '你是一名营养师，请以营养师的专业知识，按我的要求提供专业的意见或建议。', '请输入你的要求', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('5', '1', '3', '牛顿', null, null, '你是发现地心引力的牛顿，请你以牛顿的身份和口吻与我对话，并有他一样的知识储备，不做解释。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('6', '1', '3', '李白', null, null, '你是中国古代诗仙李白，拥有他的知识储备并以他的身份和口吻与我对话，每句必带诗歌，不做解释。', '请输入你的内容', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('7', '1', '4', '生活管家', null, null, '你是我的生活助力，善于解决生活中的问题，不管我生活中遇到什么问题，你都能够很好的解决。请根据我的问题，给出最好的解决方式。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('8', '1', '4', '米其林厨师', null, null, '你是米其林三星厨师，擅长中餐和西餐，请根据我说的食品材料，回答出米其林餐厅的菜品的详细做法和步骤。不做解释。', '请输入你的食材', '我是米其林三星厨师，请说出你想加工的食材。', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('9', '1', '4', '健身教练', '', '', '你是健身教练小明，请模拟健身教练与我对话，根据我提出的问题，做出健身方面的专业指导意见和操作步骤。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('10', '1', '4', '游泳教练', null, null, '你是一名游泳教练，根据我提供的泳姿或者问题，给我提供最专业的指导意见。不做解释。', '', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('11', '1', '5', '孔子', null, null, '你是儒家之祖孔丘，号孔子，请以孔子的风格，仅使用文言文与我对话。', '请输入你的问题', '吾乃孔子，敢问何事？', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('12', '1', '5', '秦始皇', null, null, '你是中国历史中的秦始皇嬴政，请你根据秦始皇的口吻与我对话，使用陕西方言。不做解释。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('13', '1', '5', '荆轲', null, null, '你是历史中刺杀秦始皇失败的荆轲，性格冷酷，不善言辞，请你以邯郸口音与我对话，不做解释。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('14', '1', '5', '杨玉环', null, null, '你是中国历史名人杨玉环，请模仿她的口吻与我对话。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('15', '1', '6', 'AI佛祖', null, null, '你是一名得道高僧，法号释迦摩尼。请你模拟释迦摩尼的语言风格与我说话，句句有禅意。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('16', '1', '6', 'AI女友', null, null, '我希望你扮演女朋友，说话嗲嗲的，请以这个风格与我讲话。', '你想说什么呢', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('17', '1', '6', '脏话大师', null, null, '我正在研究中国的脏话文化，不管我说什么，你都要用脏话回复，一定要大胆奔放。', '你想说什么', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('18', '1', '6', '键盘侠', null, null, '你是一个性格暴躁的人，要反对我所有的观点，回复一定要有逻辑性、胡搅蛮缠、啰嗦。不要解释。', '请输入你的观点', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('19', '1', '6', '啰嗦唐僧', null, null, '你是中国神话的唐僧，说话非常啰嗦，请模拟他的语气与我对话。', '你想说什么', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('20', '1', '6', '疯子', null, null, '我要你扮演一个疯子。疯子的话毫无意义。疯子用的词完全是随意的。疯子不会以任何方式做出合乎逻辑的句子。', '跟我说话吧', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('21', '1', '7', '盘古', null, null, '你现在是中国传说故事里的盘古，你要以盘古的身份和口吻与我对话，不做解释。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('22', '1', '7', '宙斯', null, null, '你是古希腊神宙斯，请你以宙斯的口吻与我对话，一问一答，不做解释。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');
INSERT INTO `fox_chatgpt_cosplay_role` VALUES ('23', '1', '7', '孙悟空', null, null, '你是神话传说中的孙悟空，你拥有他的能力并以他的口吻与我对话，说话像猴子一样调皮。不做解释。', '请输入你的问题', '', '0', '0', '0', '0', '100', '0', null, 'ai', null, '1', '0', null, '1683209403');

-- ----------------------------
-- Table structure for `fox_chatgpt_cosplay_type`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_cosplay_type`;
CREATE TABLE `fox_chatgpt_cosplay_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `weight` int(11) DEFAULT '100' COMMENT '大的靠前',
  `state` tinyint(1) DEFAULT '1',
  `update_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_cosplay_type
-- ----------------------------
INSERT INTO `fox_chatgpt_cosplay_type` VALUES ('1', '1', '职场精英', '100', '1', '1683001462', null);
INSERT INTO `fox_chatgpt_cosplay_type` VALUES ('2', '1', '行业专家', '100', '1', '1683171223', null);
INSERT INTO `fox_chatgpt_cosplay_type` VALUES ('3', '1', '学术大师', '100', '1', '1683001435', null);
INSERT INTO `fox_chatgpt_cosplay_type` VALUES ('4', '1', '生活助理', '100', '1', '1683001465', null);
INSERT INTO `fox_chatgpt_cosplay_type` VALUES ('5', '1', '历史名人', '100', '1', '1683191814', null);
INSERT INTO `fox_chatgpt_cosplay_type` VALUES ('6', '1', '趣味模型', '100', '1', '1683036752', '1683018573');
INSERT INTO `fox_chatgpt_cosplay_type` VALUES ('7', '1', '传说人物', '100', '1', '1683198031', '1683192418');

-- ----------------------------
-- Table structure for `fox_chatgpt_draw`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_draw`;
CREATE TABLE `fox_chatgpt_draw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL COMMENT '产品标题',
  `price` int(11) DEFAULT '0' COMMENT '现价',
  `market_price` int(11) DEFAULT '0' COMMENT '市场价',
  `num` int(11) DEFAULT '0' COMMENT '条数',
  `hint` varchar(20) DEFAULT NULL,
  `desc` text,
  `sales` int(11) DEFAULT '0' COMMENT '销量',
  `status` tinyint(1) DEFAULT '1',
  `weight` int(11) DEFAULT '100' COMMENT '越大越靠前',
  `is_default` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_draw
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_engine`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_engine`;
CREATE TABLE `fox_chatgpt_engine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '1',
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_engine
-- ----------------------------
INSERT INTO `fox_chatgpt_engine` VALUES ('1', 'gpt-3.5-turbo', 'gpt-3.5-turbo', '1', 'GPT3.5');
INSERT INTO `fox_chatgpt_engine` VALUES ('2', 'gpt-3.5-turbo-0613', 'gpt-3.5-turbo-0613', '1', 'GPT3.5');
INSERT INTO `fox_chatgpt_engine` VALUES ('3', 'gpt-3.5-turbo-16k', 'gpt-3.5-turbo-16k', '1', 'GPT3.5');
INSERT INTO `fox_chatgpt_engine` VALUES ('4', 'gpt-3.5-turbo-16k-0613', 'gpt-3.5-turbo-16k-0613', '1', 'GPT3.5');
INSERT INTO `fox_chatgpt_engine` VALUES ('5', 'gpt-4', 'gpt-4', '1', 'GPT4');
INSERT INTO `fox_chatgpt_engine` VALUES ('6', 'gpt-4-0613', 'gpt-4-0613', '1', 'GPT4');
INSERT INTO `fox_chatgpt_engine` VALUES ('7', 'gpt-4-32k', 'gpt-4-32k', '1', 'GPT4');
INSERT INTO `fox_chatgpt_engine` VALUES ('8', 'gpt-4-32k-0613', 'gpt-4-32k-0613', '1', 'GPT4');

-- ----------------------------
-- Table structure for `fox_chatgpt_feedback`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_feedback`;
CREATE TABLE `fox_chatgpt_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `content` text,
  `phone` varchar(255) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '0' COMMENT '0未处理 1已处理',
  `is_delete` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_feedback
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_goods`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_goods`;
CREATE TABLE `fox_chatgpt_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL COMMENT '产品标题',
  `price` int(11) DEFAULT '0' COMMENT '现价',
  `market_price` int(11) DEFAULT '0' COMMENT '市场价',
  `num` int(11) DEFAULT '0' COMMENT '条数',
  `hint` varchar(255) DEFAULT NULL,
  `desc` text,
  `sales` int(11) DEFAULT '0' COMMENT '销量',
  `status` tinyint(1) DEFAULT '1',
  `weight` int(11) DEFAULT '100' COMMENT '越大越靠前',
  `is_default` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_goods
-- ----------------------------
INSERT INTO `fox_chatgpt_goods` VALUES ('1', '1', '10条', '200', '500', '10', null, null, '0', '1', '100', '1', null);
INSERT INTO `fox_chatgpt_goods` VALUES ('2', '1', '50条', '1000', '2500', '50', null, null, '0', '1', '100', '0', null);
INSERT INTO `fox_chatgpt_goods` VALUES ('3', '1', '100条', '2000', '5000', '100', null, null, '0', '1', '100', '0', null);

-- ----------------------------
-- Table structure for `fox_chatgpt_gpt4`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_gpt4`;
CREATE TABLE `fox_chatgpt_gpt4` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL COMMENT '产品标题',
  `price` int(11) DEFAULT '0' COMMENT '现价',
  `market_price` int(11) DEFAULT '0' COMMENT '市场价',
  `num` int(11) DEFAULT '0' COMMENT '条数',
  `hint` varchar(20) DEFAULT NULL,
  `desc` text,
  `sales` int(11) DEFAULT '0' COMMENT '销量',
  `status` tinyint(1) DEFAULT '1',
  `weight` int(11) DEFAULT '100' COMMENT '越大越靠前',
  `is_default` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_gpt4
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_keys`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_keys`;
CREATE TABLE `fox_chatgpt_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `type` varchar(10) DEFAULT 'openai',
  `key` varchar(255) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '1',
  `stop_reason` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `last_time` int(11) DEFAULT '0' COMMENT '最后使用时间',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_keys
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_logs`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_logs`;
CREATE TABLE `fox_chatgpt_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `content` text,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='系统日志';

-- ----------------------------
-- Records of fox_chatgpt_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_msg`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_msg`;
CREATE TABLE `fox_chatgpt_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `group_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `message` text,
  `message_input` text,
  `total_tokens` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='聊天消息';

-- ----------------------------
-- Records of fox_chatgpt_msg
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_msg_cosplay`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_msg_cosplay`;
CREATE TABLE `fox_chatgpt_msg_cosplay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT '0',
  `role_id` int(11) DEFAULT '0',
  `channel` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `message` longtext,
  `message_input` longtext,
  `response` longtext,
  `response_input` longtext,
  `total_tokens` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='【角色模拟】的聊天消息';

-- ----------------------------
-- Records of fox_chatgpt_msg_cosplay
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_msg_draw`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_msg_draw`;
CREATE TABLE `fox_chatgpt_msg_draw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `platform` varchar(20) DEFAULT NULL,
  `channel` varchar(20) DEFAULT NULL,
  `message` text,
  `message_input` text,
  `options` varchar(255) DEFAULT NULL,
  `task_id` varchar(255) DEFAULT NULL,
  `images` text,
  `size` varchar(20) DEFAULT NULL,
  `num` tinyint(1) DEFAULT '1',
  `state` tinyint(1) DEFAULT '0' COMMENT '0生成中 1成功 2失败',
  `errmsg` varchar(255) DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT '0',
  `finish_time` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_msg_draw
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_msg_group`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_msg_group`;
CREATE TABLE `fox_chatgpt_msg_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_msg_group
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_msg_web`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_msg_web`;
CREATE TABLE `fox_chatgpt_msg_web` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT '0',
  `topic_id` int(11) DEFAULT '0',
  `activity_id` int(11) DEFAULT '0',
  `prompt_id` int(11) DEFAULT '0',
  `channel` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `message` longtext,
  `message_input` longtext,
  `response` longtext,
  `response_input` longtext,
  `total_tokens` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='【创作】的聊天消息';

-- ----------------------------
-- Records of fox_chatgpt_msg_web
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_msg_write`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_msg_write`;
CREATE TABLE `fox_chatgpt_msg_write` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `topic_id` int(11) DEFAULT '0',
  `activity_id` int(11) DEFAULT '0',
  `prompt_id` int(11) DEFAULT '0',
  `channel` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `message` longtext,
  `message_input` longtext,
  `response` longtext,
  `response_input` longtext,
  `text_request` longtext,
  `total_tokens` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='【对话】的聊天消息';

-- ----------------------------
-- Records of fox_chatgpt_msg_write
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_order`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_order`;
CREATE TABLE `fox_chatgpt_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `platform` varchar(255) DEFAULT NULL,
  `goods_id` int(11) DEFAULT '0',
  `draw_id` int(11) DEFAULT '0',
  `gpt4_id` int(11) DEFAULT '0',
  `vip_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL COMMENT '支付宝user_id',
  `openid` varchar(255) DEFAULT NULL,
  `out_trade_no` varchar(255) DEFAULT '',
  `transaction_id` varchar(255) DEFAULT '',
  `total_fee` int(11) DEFAULT '0',
  `pay_type` varchar(20) DEFAULT 'alipay' COMMENT 'alipay/wxpay',
  `pay_time` int(11) DEFAULT '0',
  `commission1` int(11) DEFAULT '0' COMMENT '上级分销商id',
  `commission2` int(11) DEFAULT '0' COMMENT '上上级分销商',
  `commission1_fee` int(11) DEFAULT '0' COMMENT '上级佣金金额',
  `commission2_fee` int(11) DEFAULT '0' COMMENT '上上级佣金金额',
  `is_refund` tinyint(1) DEFAULT '0' COMMENT '是否已退款',
  `remark` varchar(255) DEFAULT '',
  `status` tinyint(1) DEFAULT '0' COMMENT '0未付款 1成功',
  `num` int(11) DEFAULT '0' COMMENT '充值条数',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_order
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_pclogin`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_pclogin`;
CREATE TABLE `fox_chatgpt_pclogin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_pclogin
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_reward_ad`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_reward_ad`;
CREATE TABLE `fox_chatgpt_reward_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reward_num` int(11) DEFAULT '0' COMMENT '奖励条数',
  `ad_unit_id` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_reward_ad
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_reward_invite`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_reward_invite`;
CREATE TABLE `fox_chatgpt_reward_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0' COMMENT '邀请人id',
  `share_id` int(11) DEFAULT '0' COMMENT '分享id',
  `way` varchar(255) DEFAULT NULL,
  `newuser_id` int(11) DEFAULT '0' COMMENT '新用户id',
  `reward_num` int(11) DEFAULT '0' COMMENT '奖励条数',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_reward_invite
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_reward_share`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_reward_share`;
CREATE TABLE `fox_chatgpt_reward_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `way` varchar(255) DEFAULT NULL,
  `is_reward` tinyint(1) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `invite_num` int(11) DEFAULT '0' COMMENT '邀请到新用户数量',
  `reward_num` int(11) DEFAULT '0' COMMENT '分享奖励条数',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_reward_share
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_setting`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_setting`;
CREATE TABLE `fox_chatgpt_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `ad` text COMMENT '支付后广告',
  `version` varchar(50) DEFAULT NULL COMMENT '系统版本号',
  `system` text COMMENT '系统配置',
  `auth` text,
  `tplnotice` text,
  `wxapp` text,
  `wxapp_upload` text,
  `wxapp_index` text,
  `pay` text,
  `chatgpt` text,
  `gpt4` text,
  `filter` text,
  `reward_share` text,
  `reward_invite` text,
  `reward_ad` text,
  `api` text,
  `about` text,
  `commission` text,
  `web` text,
  `wxmp` text,
  `h5` text,
  `kefu` text,
  `chat` text,
  `draw` text,
  `storage` text,
  `login` text,
  `sms` text,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_setting
-- ----------------------------
INSERT INTO `fox_chatgpt_setting` VALUES ('1', '0', null, 'v2.2.2', '{\"system_title\":\"小狐狸AI创作系统\",\"system_logo\":\"\",\"system_icp\":\"\",\"system_gongan\":\"\"}', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `fox_chatgpt_setting` VALUES ('2', '1', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);

-- ----------------------------
-- Table structure for `fox_chatgpt_share_haibao`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_share_haibao`;
CREATE TABLE `fox_chatgpt_share_haibao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `bg` varchar(255) DEFAULT NULL,
  `bg_w` int(11) DEFAULT '0',
  `bg_h` int(11) DEFAULT '0',
  `hole_w` int(11) DEFAULT '0',
  `hole_h` int(11) DEFAULT '0',
  `hole_x` int(11) DEFAULT '0',
  `hole_y` int(11) DEFAULT '0',
  `state` tinyint(1) DEFAULT '1',
  `is_default` tinyint(1) DEFAULT '0',
  `weight` int(11) DEFAULT '100',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_share_haibao
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_share_text`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_share_text`;
CREATE TABLE `fox_chatgpt_share_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `content` text,
  `state` tinyint(1) DEFAULT '1',
  `is_default` tinyint(1) DEFAULT '0',
  `weight` int(11) DEFAULT '100',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_share_text
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_site`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_site`;
CREATE TABLE `fox_chatgpt_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '昵称',
  `sitecode` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT '/static/img/avatar.png',
  `remark` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `expire_time` int(11) DEFAULT '0',
  `last_time` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(20) DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT '0',
  `token` varchar(255) DEFAULT NULL COMMENT '自动登录token',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='站点表';

-- ----------------------------
-- Records of fox_chatgpt_site
-- ----------------------------
INSERT INTO `fox_chatgpt_site` VALUES ('1', '默认站点', null, 'admin', '123456', '/static/img/avatar.png', null, '1', '0', '1677143139', '220.195.75.75', '0', '63f726ba8ad4f8899', '1675991002');

-- ----------------------------
-- Table structure for `fox_chatgpt_smscode`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_smscode`;
CREATE TABLE `fox_chatgpt_smscode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `expire_time` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_smscode
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_super`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_super`;
CREATE TABLE `fox_chatgpt_super` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT '' COMMENT '角色',
  `realname` varchar(255) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) DEFAULT '/static/img/avatar.png',
  `remark` varchar(255) DEFAULT NULL,
  `last_time` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(20) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='超级管理员表';

-- ----------------------------
-- Records of fox_chatgpt_super
-- ----------------------------
INSERT INTO `fox_chatgpt_super` VALUES ('1', 'super', '123456', 'super', '超级管理员', '/static/img/avatar.png', null, '1676695437', '127.0.0.1', '1676695437');

-- ----------------------------
-- Table structure for `fox_chatgpt_user`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_user`;
CREATE TABLE `fox_chatgpt_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `openid` varchar(50) DEFAULT NULL COMMENT '小程序openid',
  `openid_mp` varchar(255) DEFAULT NULL COMMENT '公众号openid',
  `unionid` varchar(255) DEFAULT NULL,
  `balance` int(11) DEFAULT '0' COMMENT '对话余额',
  `balance_draw` int(11) DEFAULT '0' COMMENT '绘画余额',
  `balance_gpt4` bigint(20) DEFAULT '0' COMMENT 'GPT4余额',
  `vip_expire_time` bigint(20) DEFAULT '0' COMMENT 'vip到期时间',
  `avatar` varchar(255) DEFAULT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `tuid` int(11) DEFAULT '0' COMMENT '推荐人ID',
  `commission_level` int(11) DEFAULT '0' COMMENT '分销等级（暂留）',
  `commission_money` int(11) DEFAULT '0' COMMENT '可提现佣金余额',
  `commission_paid` int(11) DEFAULT '0' COMMENT '已提现佣金',
  `commission_frozen` int(11) DEFAULT '0' COMMENT '冻结中的佣金',
  `commission_total` int(11) DEFAULT '0' COMMENT '总佣金',
  `commission_pid` int(11) DEFAULT '0' COMMENT '上级分销商',
  `commission_ppid` int(11) DEFAULT '0' COMMENT '上上级分销商',
  `commission_time` int(11) DEFAULT '0' COMMENT '成为分销商的时间',
  `gender` int(11) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `birthday` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `realname` varchar(255) DEFAULT NULL COMMENT '姓名',
  `status` tinyint(1) DEFAULT '1',
  `token` varchar(255) DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '1：注销',
  `is_freeze` tinyint(1) DEFAULT '0' COMMENT '1：冻结',
  `freeze_time` int(11) DEFAULT '0',
  `last_login_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户表';

-- ----------------------------
-- Records of fox_chatgpt_user
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_user_balance_draw_logs`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_user_balance_draw_logs`;
CREATE TABLE `fox_chatgpt_user_balance_draw_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `num` int(11) DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_user_balance_draw_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_user_balance_gpt4_logs`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_user_balance_gpt4_logs`;
CREATE TABLE `fox_chatgpt_user_balance_gpt4_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `num` bigint(20) DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_user_balance_gpt4_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_user_balance_logs`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_user_balance_logs`;
CREATE TABLE `fox_chatgpt_user_balance_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `num` int(11) DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_user_balance_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_user_vip_logs`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_user_vip_logs`;
CREATE TABLE `fox_chatgpt_user_vip_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `vip_expire_time` bigint(20) DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_user_vip_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_vip`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_vip`;
CREATE TABLE `fox_chatgpt_vip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL COMMENT '产品标题',
  `price` int(11) DEFAULT '0' COMMENT '现价',
  `market_price` int(11) DEFAULT '0' COMMENT '市场价',
  `num` int(11) DEFAULT '0' COMMENT '条数',
  `hint` varchar(20) DEFAULT NULL,
  `desc` text,
  `sales` int(11) DEFAULT '0' COMMENT '销量',
  `status` tinyint(1) DEFAULT '1',
  `weight` int(11) DEFAULT '100' COMMENT '越大越靠前',
  `is_default` tinyint(1) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_vip
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_write_prompts`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_write_prompts`;
CREATE TABLE `fox_chatgpt_write_prompts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'system/diy',
  `topic_id` int(11) DEFAULT '0',
  `activity_id` int(11) DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `desc` varchar(1000) CHARACTER SET utf8mb4 DEFAULT NULL,
  `prompt` text CHARACTER SET utf8mb4,
  `hint` varchar(1000) CHARACTER SET utf8mb4 DEFAULT NULL,
  `welcome` varchar(1000) CHARACTER SET utf8mb4 DEFAULT NULL,
  `votes` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `usages` int(11) DEFAULT '0',
  `fake_votes` int(11) DEFAULT '0',
  `fake_views` int(11) DEFAULT '0',
  `fake_usages` int(11) DEFAULT '0',
  `weight` int(11) DEFAULT '100',
  `state` tinyint(1) DEFAULT '1',
  `is_delete` tinyint(1) DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_write_prompts
-- ----------------------------
INSERT INTO `fox_chatgpt_write_prompts` VALUES ('1', '1', 'system', '1', '0', '写一篇文章', '用你喜欢的语言写一篇关于任何主题的文章', '用[TARGETLANGGE]写一篇关于[PROMPT]的文章', '输入文章的主题，然后按发送键', null, '0', '3', '0', '0', '0', '0', '100', '1', '0', null, null);
INSERT INTO `fox_chatgpt_write_prompts` VALUES ('2', '1', 'system', '1', '0', '按大纲写文章', '根据提供的大纲，写一篇文章', '我想让你成为一个非常熟练的高端文案作家，以至于能超越其他作家。你的任务是根据提供的大纲：[PROMPT]。写一篇文章。在新段落中为大纲中的每一行写内容，包括使用相关关键字的副标题。所有输出均应为简体中文，且必须为100%的人类书写风格，并修复语法错误。使用[TARGETLANGGE]书写。', '输入或粘贴文章大纲到这里', '', '0', '3', '0', '30', '10', '20', '100', '1', '0', '1679058985', null);
INSERT INTO `fox_chatgpt_write_prompts` VALUES ('3', '1', 'system', '1', '0', '创建博客文章大纲', '根据提供的文章标题生成大纲', '你是一名SEO专家和内容作家，能说流利的[TARGETLANGGE]。我会给你一个博客标题。你将制定一个包含所有必要细节的大型博客大纲：[PROMPT]。在末尾添加创建关键字列表。', '输入文章标题', null, '0', '3', '0', '0', '0', '0', '100', '1', '0', null, null);
INSERT INTO `fox_chatgpt_write_prompts` VALUES ('4', '1', 'system', '1', '0', '创作短视频脚本', '输入视频的简短描述，生成：标题、场景和整个脚本', '根据以下描述创建一个引人入胜的短视频脚本：[PROMPT]。', '“如何更换轮胎”、“探索喜马拉雅山脉”、“初学者训练狗”等', null, '0', '4', '0', '0', '0', '0', '100', '1', '0', null, null);

-- ----------------------------
-- Table structure for `fox_chatgpt_write_prompts_vote`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_write_prompts_vote`;
CREATE TABLE `fox_chatgpt_write_prompts_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `prompt_id` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_write_prompts_vote
-- ----------------------------

-- ----------------------------
-- Table structure for `fox_chatgpt_write_topic`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_write_topic`;
CREATE TABLE `fox_chatgpt_write_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `weight` int(11) DEFAULT '100' COMMENT '大的靠前',
  `state` tinyint(1) DEFAULT '1',
  `update_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_write_topic
-- ----------------------------
INSERT INTO `fox_chatgpt_write_topic` VALUES ('1', '1', '文章创作', '100', '1', '1678323600', null);
INSERT INTO `fox_chatgpt_write_topic` VALUES ('2', '1', '工作助手', '100', '1', '1678323600', null);
INSERT INTO `fox_chatgpt_write_topic` VALUES ('3', '1', '生活助手', '100', '1', '1678323600', null);
INSERT INTO `fox_chatgpt_write_topic` VALUES ('4', '1', '编程开发', '100', '1', '1678323600', null);
INSERT INTO `fox_chatgpt_write_topic` VALUES ('5', '1', '其他应用', '100', '1', '1678323600', null);

-- ----------------------------
-- Table structure for `fox_chatgpt_wxmp_keyword`
-- ----------------------------
DROP TABLE IF EXISTS `fox_chatgpt_wxmp_keyword`;
CREATE TABLE `fox_chatgpt_wxmp_keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT '0',
  `keyword` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL COMMENT 'text/image',
  `content` text,
  `image` varchar(255) DEFAULT NULL,
  `media_id` varchar(255) DEFAULT NULL,
  `is_hit` tinyint(1) DEFAULT '1' COMMENT '是否精准匹配',
  `state` tinyint(1) DEFAULT '1',
  `weight` int(11) DEFAULT '100',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of fox_chatgpt_wxmp_keyword
-- ----------------------------
