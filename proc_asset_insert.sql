USE jizhang;
DROP PROCEDURE IF EXISTS proc_asset_insert;
DELIMITER $
CREATE  PROCEDURE proc_asset_insert(IN time0 int,IN money0 int,IN zhifu0 int,IN ufid0 int,IN paymethod int,IN acid int)

BEGIN
	DECLARE time1 int(11) default 0;
	DECLARE time2 varchar(100) default '';
	DECLARE money1 int(5) default 0;
	DECLARE maxid int(11) default 0;
	DECLARE sum7 int(11) default 0;
	DECLARE day0 int(5) default 0;
	DECLARE day1 int(5) default 0;
	DECLARE i int default 1;
	DECLARE errormsg varchar(100) default '';

--	创建以paymethod为后缀的分类记录表，记录每个paymethod下的收支、转账、账户余额信息
--	如：操作人张三名下建行储蓄卡的相关记录
--  --------------------------------------------------------------------
--	number 作为每一种（ufid，as_type）组合下的记录编号
--  as_time 为每笔交易记录的时间，unix格式
--  as_time_ymd 为每笔交易记录的时间，年月日格式
--  as_type 1代表收入 2代表支出 4代表内部转账和还款
--	ufid 账户持有者id，默认会建1~5共5个账户，最多5个账户
--	acid jizhang_account中本次交易的id
--	as_money 金额
--  as_balance 余额
--  关于余额：
--  1.余额为每笔记录的余额；
--  2.当添加一条记录时，先判断此记录的时间，如果是最新的记录，则余额等于此记录前最近一次记录的加减本次记录的收支（由于以按天数为时间
--    精度进行记录的，所以判断同一天内最近一笔记录还要参考assetid或者number）;
--  3.如果不是最新的记录，则将本次记录之后所有的记录都加减本记录的收支；
--  4.余额不可编辑，可以通过增加一笔收支来补齐。
--  5.余额分为收入、支出、内部转账三大类，即本账户所有收入总和、所有支出总和、所有内部转账（转入和转出）的总和。
--	modify_info 人为修改记录时写的备注信息
--	modify_time 人为修改记录的时间
--  ---------------------------------------------------------------------
--  如果jizhang_asset表不存在则建一个空表
	set @querytable = concat('jizhang_asset_',cast(paymethod as char));
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
					 
--  动态sql执行的标准方法
	prepare stmt from @sql1;
	execute stmt;
	deallocate prepare stmt;
--  查看是不是空表	
	set @sql1 = concat('select count(*) into @keycountTemp from ',@querytable,';');
	prepare stmt from @sql1;
	execute stmt;
	deallocate prepare stmt;
	
--  获取分类记录表中主键的记录数
	set @keycount = @keycountTemp;
	
--  主键记录为0说明是空表，则初始化
--  时间初始化为2017-01-01 23：59：59
--  只有5个用户,ufid = 1,2,3,4,5
--  初始化ufid，as_type的15种组合,硬编码，惭愧-_-!
	if(@keycount = 0)
	then 
		set @sql1 = concat('insert into ',
					   @querytable,
					   ' (number,as_time,as_time_ymd,as_type,acid,as_money,as_balance,ufid,modify_info,modify_time) ',
					   'values ',

					   '(1,1483200000,"2017-01-01",1,0,0,0,1,0,0),',
					   '(1,1483200000,"2017-01-01",2,0,0,0,1,0,0),',
					   '(1,1483200000,"2017-01-01",4,0,0,0,1,0,0),',
					   '(1,1483200000,"2017-01-01",1,0,0,0,2,0,0),',
					   '(1,1483200000,"2017-01-01",2,0,0,0,2,0,0),',
					   '(1,1483200000,"2017-01-01",4,0,0,0,2,0,0),',

					   '(1,1483200000,"2017-01-01",1,0,0,0,3,0,0),',
					   '(1,1483200000,"2017-01-01",2,0,0,0,3,0,0),',
					   '(1,1483200000,"2017-01-01",4,0,0,0,3,0,0),',
					   '(1,1483200000,"2017-01-01",1,0,0,0,4,0,0),',
					   '(1,1483200000,"2017-01-01",2,0,0,0,4,0,0),',
					   '(1,1483200000,"2017-01-01",4,0,0,0,4,0,0),',

					   '(1,1483200000,"2017-01-01",1,0,0,0,5,0,0),',
					   '(1,1483200000,"2017-01-01",2,0,0,0,5,0,0),',
					   '(1,1483200000,"2017-01-01",4,0,0,0,5,0,0)'
	                  );
		prepare stmt from @sql1;
		execute stmt;
		deallocate prepare stmt;
	end if;
--  不是空表则开始插入
	set time1 = time0;
	set time2 = from_unixtime(time1,"%Y-%m-%d");
	set money1 = money0;

	set @time1 = time1;
	set @time2 = time2;
	set @money = money1;
	set @ufid = ufid0;
	set @zhifu = zhifu0;
	set @acid = acid;

	--  获取当前ufid，as_type组合下的最近一次的记录编号
	set @sql1 = concat('SELECT MAX(number) into @maxid0 FROM ',@querytable,' where ufid = ? and as_type = ?;');
	prepare stmt from @sql1;
	execute stmt using @ufid,@zhifu;
	deallocate prepare stmt;
	set maxid = @maxid0;


	set @sql1 = concat('select count(assetid) into @latercountTemp from ',@querytable,' where as_time > ? and ufid = ? and  as_type= ?;');
	prepare stmt from @sql1;
	execute stmt using @time1,@ufid,@zhifu;
	deallocate prepare stmt;
	if @latercountTemp is null then set @latercountTemp = -1;
	end if;
	set @latercount = @latercountTemp;
	/*--------------------------------------------------------------------*/
	-- 时间上最新的记录
	-- 时间精度为天数，当天的各笔记录可直接增加，不分先后，余额传递和计算
	if(@latercount = 0)
	then
	--  获取当前ufid，as_type组合下的最近一次记录的时间和余额
	set @sql1= concat('SELECT as_time,as_balance INTO @lasttimeTemp,@lastbalanceTemp FROM ',@querytable,
						' WHERE number = ? and ufid = ? and as_type = ?;');
	prepare stmt from @sql1;
	set @maxid = @maxid0;
	execute stmt using @maxid,@ufid,@zhifu;
	deallocate prepare stmt;
	if @lasttimeTemp is null then set @lasttimeTemp = 0;
	end if;
	set @lasttime = @lasttimeTemp;
	if @lastbalanceTemp is null then set @lastbalanceTemp = 0;
	end if;
	set @lastbalance = @lastbalanceTemp;

	-- 增加此记录
	set @sql1 = concat('insert into ',
				@querytable,
				' (number,as_time,as_time_ymd,as_type,acid,as_money,as_balance,ufid,modify_info,modify_time) ',
				'values ',
				'(?,?,?,?,?,?,?,?,0,0);'
				);
	set @number = maxid + 1;
	set @time1 = time1;
	set @time2 = time2;
	set @money = money1;
	set @balance = @lastbalance + @money;
	prepare stmt from @sql1;
	execute stmt using @number,@time1,@time2,@zhifu,@acid,@money,@balance,@ufid;
	deallocate prepare stmt;
	end if;
	/*-------------------------------------------------------------------------*/
	-- 时间上不是最新的记录
	-- 以此笔记录当天（或离此笔记录时间最近一天）的所有记录中number值最大的记录点为参考点，获取其余额
	-- 记录此笔记录，此笔记录的余额为上述获取的余额减去此笔记录金额
	-- 时间上在此笔记录之后的所有记录的余额更新
	if(@latercount > 0)
	then
	set @sql1 = concat('select number,as_balance into @numberTemp,@lastbalanceTemp from ',
				@querytable,
				' where as_time <= ? and ufid = ? and as_type = ? order by number desc limit 1'
				);
	prepare stmt from @sql1;
	execute stmt using @time1,@ufid,@zhifu;
	deallocate prepare stmt;
	if @numberTemp is null then set @numberTemp = 0;
	end if;
	set @number = @numberTemp;
	if @lastbalanceTemp is null then set @lasttbalanceTemp = 0;
	end if;
	set @lastbalance = @lastbalanceTemp;

	-- 更新本记录后的所有记录
	-- 参考点之后的每个已有记录点的number值加1，为新增记录空出位置
	-- 如果按照number值从小到大加1，则会出现重复的情况，因此按照number值从大到小加1
	set @numberMin = @number + 1;
	set @numberPointer = maxid;
	while @numberPointer >= @numberMin do
		set @sql1 = concat('select assetid,as_balance into @assetidTemp,@laterbalanceTemp from ',
				@querytable,
				' where number = ? and ufid = ? and as_type = ?;'
				);
		prepare stmt from @sql1;
		execute stmt using @numberPointer,@ufid,@zhifu;
		deallocate prepare stmt;
		if @assetidTemp is null then set @assetidTemp = 0;
		end if;
		set @assetid = @assetidTemp;
		if @laterbalanceTemp is null then set @laterbalanceTemp = 0;
		end if;
		set @laterbalance = @laterbalanceTemp;
		set @numberNew = @numberPointer + 1;
		set @laterbalance = @laterbalance + @money;

		set @sql1 = concat('update ',
				@querytable,
				' set number = ? , as_balance = ? where assetid = ? and ufid = ? and as_type = ?;'
				);
		prepare stmt from @sql1;
		execute stmt using @numberNew,@laterbalance,@assetid,@ufid,@zhifu;
		deallocate prepare stmt;
		set @numberPointer = @numberPointer - 1;
	end while;	
	-- 插入本记录
	set @sql1 = concat('insert into ',
				@querytable,
				' (number,as_time,as_time_ymd,as_type,acid,as_money,as_balance,ufid,modify_info,modify_time) ',
				'values ',
				'(?,?,?,?,?,?,?,?,0,0);'
				);
	set @balance = @lastbalance + @money;
	set @numberPointer = @number + 1;
	prepare stmt from @sql1;
	execute stmt using @numberPointer,@time1,@time2,@zhifu,@acid,@money,@balance,@ufid;
	deallocate prepare stmt;

	end if;

	END
	$
	DELIMITER ;