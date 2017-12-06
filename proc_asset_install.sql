USE jizhang;
DROP PROCEDURE IF EXISTS proc_asset_install;
DELIMITER $
CREATE  PROCEDURE proc_asset_install(IN ufid0 int)
BEGIN
-- 读取jizhang_account中的所有记录，分类更新或新建jizhang_asset表,
-- 网站初始化时逐个调用各个ufid下的proc_asset_install()，
-- 读取jizhang_account_class中classtype等于3或4的classid作为paymethod
-- 逐个刷新jizhang_asset_paymethod表，没有的话就创建，
-- 用到mysql游标遍历jizhang_accout中符合当前jizhang_asset_paymethod表的记录
-- 用到handler指定中断的条件,将中断的标志绑定到游标
-- 1.遍历读取jizhang_account_class中classtype等于3或4的classid作为paymethod；
-- 2.判断jizhang_asset_paymethod表是否存在，不存在则创建
-- 3.存在则进行更新
-- --3.1.逐条读取jizhang_account中所有acpaymethod等于paymethod、本用户下的记录
-- --3.2.调用proc_asset_edit()的“2”编辑功能，已有的更新，没有的新增
declare done1 boolean default 0;

declare t_paymethod int default 0;
declare p_paymethod int default 0;

declare t_index cursor for select classid from jizhang_account_class where classtype = 3 or classtype = 4;
declare p_index cursor for select classid from jizhang_account_class where classtype = 4;

declare continue handler for sqlstate '02000' set done1 = 1;

open t_index;
repeat
-- 游标遍历jizhang_account_class结果集
	fetch t_index into t_paymethod;
	if done1 != 1 then
		-- 判断jizhang_asset_paymethod是否存在
		set @querytable = concat('jizhang_asset_',cast(t_paymethod as char));
		select count(table_name) into @temp from information_schema.tables where table_schema='jizhang' and table_name = @querytable;
		-- 不存在则创建一个空表
		if(@temp = 0)
		then 
			set @sql1 = concat('CREATE TABLE IF NOT EXISTS ',
						  @querytable,
						  '(',
						  'assetid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,',
						  'number int(5) DEFAULT 1,',
						  'as_time int(11) DEFAULT 1483200000,',
						  'as_time_ymd varchar(20) DEFAULT "2017-01-01 00:00:00",',
						  'as_type int(5) DEFAULT 0,',
						  'acid int(11) NOT NULL DEFAULT 0,',
						  'as_money int(5) DEFAULT 0,',
						  'as_balance int(5) DEFAULT 0,',
						  'ufid int(5) DEFAULT 0,',
						  'modify_info varchar(50) DEFAULT 0,',
						  'modify_time int(11) DEFAULT 0',
						  ')ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;'
						 );
			prepare stmt from @sql1;
			execute stmt;
			deallocate prepare stmt;
		end if;
		
		
		-- 刷新jizhang_asset_paymethod的数据
		-- 用到游标和视图
		-- 用游标遍历jizhang_account中acpaymethod等于paymethod、本用户下的记录
		-- 更新对应的jizhang_account_paymethod表
		BEGIN
		declare done2 boolean default 0;
		declare t_acid int default 0;
		declare t_acmoney int default 0;
		declare t_actime int default 0;
		declare t_zhifu int default 0;
		declare tt_index cursor for select acid,acmoney,actime,zhifu from jizhang_account where acpaymethod = t_paymethod and jiid = ufid0;
		
		declare continue handler for sqlstate '02000' set done2 = 1;

		/* declare tt_index cursor for select assetid from view_temp;
		set @querytable = concat('jizhang_asset_',cast(t_paymethod as char));
		drop view if exists view_temp;
		set @sql= concat('create view view_temp as select * from ',@querytable,';');
		prepare stmt from @sql;
		execute stmt;
		deallocate prepare stmt;*/
		
		open tt_index;
			repeat
			-- 调用proc_asset_edit()的“2”编辑功能，已有的更新，没有的新增
			-- 利用游标将记录循环插入jizhang_asset_paymethod表中
			fetch tt_index into t_acid,t_acmoney,t_actime,t_zhifu;
			if done2 != 1 then
				call proc_asset_edit(t_actime,t_acmoney,t_zhifu,ufid0,t_paymethod,t_acid,2);
			end if;
		until done2 end repeat;
		close tt_index;
		END;
	end if;	
until done1 end repeat;
close t_index;

-- 对于内部转账这种涉及两个账户的操作，应该有两次记录，即一个户头出，同时另一个户头进
-- 而在上述循环中，只统计到了内部转账户头的出账（由于采用acpaymethod查询）
-- 因此下面再循环一次，把内部转账户头的入账记录进来
set done1 = 0;
open p_index;
repeat
	fetch p_index into p_paymethod;
	if done1 != 1 then
		set @querytable = concat('jizhang_asset_',cast(p_paymethod as char));
		BEGIN
		declare done3 boolean default 0;
		declare p_acid int default 0;
		declare p_acmoney int default 0;
		declare p_acclassid int default 0;
		declare p_actime int default 0;
		declare p_zhifu int default 0;
		declare pp_index cursor for select acid,acmoney,acclassid,actime,zhifu from jizhang_account where acclassid = p_paymethod and jiid = ufid0;
		
		declare continue handler for sqlstate '02000' set done3 = 1;

		open pp_index;
			repeat
			-- 调用proc_asset_edit()的“2”编辑功能，已有的更新，没有的新增
			-- 利用游标将记录循环插入jizhang_asset_paymethod表中
			fetch pp_index into p_acid,p_acmoney,p_acclassid,p_actime,p_zhifu;
			set p_acmoney = -1 * p_acmoney;
			if done3 != 1 then
				call proc_asset_edit(p_actime,p_acmoney,p_zhifu,ufid0,p_acclassid,p_acid,2);
			end if;
		until done3 end repeat;
		close pp_index;
		END;
	end if;
until done1 end repeat;
close p_index;

END
$
DELIMITER ;