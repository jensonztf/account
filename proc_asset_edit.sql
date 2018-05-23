USE jizhang;
DROP PROCEDURE IF EXISTS proc_asset_edit;
DELIMITER $
CREATE  PROCEDURE proc_asset_edit(IN time0 int,IN money0 int,IN zhifu0 int,IN ufid0 int,IN paymethod int,IN acid int,IN func int)
-- func 代表不同的操作
-- func=1 删除
-- func=2 编辑
-- func=3 刷新
BEGIN

declare t_paymethod int default 0;

set @time0 = time0;
set @money0 = money0;
set @zhifu0 = zhifu0;
set @ufid0 = ufid0;
set @paymethod = paymethod;
set @acid = acid;

set @querytable = concat('jizhang_asset_',cast(paymethod as char));
select count(table_name) into @tablecount from information_schema.tables where table_schema='jizhang' and table_name = @querytable;
-- jizhang_asset表存在则进行编辑操作
if(@tablecount > 0)
then
-- 删除
-- jizhang_account中某项记录删除前
-- 1，查询此记录相关信息，调用proc_asset_edit，在shanchu.php中实现
-- 2, 根据acid找到此记录，删除jizhang_asset中本记录，在本存储过程中实现
-- 3，更新本记录之后的所有记录的余额和number
-- 4，删除jizhang_account中的记录，在shanchu.php中实现
if(func = 1)
	then
	-- 查询待删除记录
	set @sql1 = concat('select number into @numberTemp from ',
			   @querytable,
			   ' where acid = ? and ufid = ? and as_type = ?'
			   );
	prepare stmt from @sql1;
	execute stmt using @acid,@ufid0,@zhifu0;
	deallocate prepare stmt;
	if @numberTemp is null then set @numberTemp = 0;
	end if;
	set @number = @numberTemp;
	-- 删除
	set @sql1 = concat('delete from ',
			   @querytable,
			   ' where acid = ? and ufid = ? and as_type = ?'
			   );
	prepare stmt from @sql1;
	execute stmt using @acid,@ufid0,@zhifu0;
	deallocate prepare stmt;
	-- 更新
	set @sql1 = concat('SELECT MAX(number) into @maxidTemp FROM ',@querytable,' where ufid = ? and as_type = ?;');
	prepare stmt from @sql1;
	execute stmt using @ufid0,@zhifu0;
	deallocate prepare stmt;
	if @maxidTemp is null then set @maxidTemp = 0;
	end if;
	set @maxid = @maxidTemp;
	set @numberPointer = @number + 1;
	while @numberPointer <= @maxid do
		set @sql1 = concat('select assetid,as_balance into @assetidTemp,@laterbalanceTemp from ',
				@querytable,
				' where number = ? and ufid = ? and as_type = ?;'
				);
		prepare stmt from @sql1;
		execute stmt using @numberPointer,@ufid0,@zhifu0;
		deallocate prepare stmt;
		if @assetidTemp is null then set @assetidTemp = 0;
		end if;
		set @assetid = @assetidTemp;
		if @laterbalanceTemp is null then set @laterbalanceTemp = 0;
		end if;
		set @laterbalance = @laterbalanceTemp;
		set @numberPointer0 = @numberPointer - 1;
		set @numberPointer = @numberPointer + 1;
		-- 删除时为 -@money,添加时为 +@money
		set @balance = @laterbalance - @money0;

		set @sql1 = concat('update ',
				@querytable,
				' set number = ? , as_balance = ? where assetid = ? and ufid = ? and as_type = ?;'
				);
		prepare stmt from @sql1;
		execute stmt using @numberPointer0,@balance,@assetid,@ufid0,@zhifu0;
		deallocate prepare stmt;
	end while;
end if;


-- 编辑
-- jizhang_account中某项记录修改前
-- 1，查询此记录相关信息，调用proc_asset_edit，在xiugai.php中实现，要求传入的金额为变化后的金额
-- 2, 根据acid找到此记录，修改jizhang_asset中本记录，在本存储过程中实现，
-- 3，如果找不到本记录，则调用proc_asset_insert新增进去，此功能可帮助完成proc_asset_install的实现
-- 3，更新本记录之后的所有记录的余额
-- 4，修改jizhang_account记录，在xiugai.php中实现
if(func =2)
then
	set @sql1 = concat('select count(assetid) into @assetidcountTemp from ',
				@querytable,
				' where acid = ? and ufid = ? and as_type =?'
				);
	prepare stmt from @sql1;
	execute stmt using @acid,@ufid0,@zhifu0;
	deallocate prepare stmt;
	if @assetidcountTemp is null then set @assetidcountTemp = -1;
	end if;
	set @assetidcount = @assetidcountTemp;
	-- 找不到本记录则新增进去
	if(@assetidcount = 0)
	then
		call proc_asset_insert(@time0,@money0,@zhifu0,@ufid0,@paymethod,@acid);
	end if;
	-- 找到了则进行修改
	if(@assetidcount > 0)
	then
	-- 查询待修改记录
	set @sql1 = concat('select number,as_money,as_balance into @numberTemp,@moneyTemp,@balanceTemp from ',
			   @querytable,
			   ' where acid = ? and ufid = ? and as_type = ?'
			   );
	prepare stmt from @sql1;
	execute stmt using @acid,@ufid0,@zhifu0;
	deallocate prepare stmt;
	if @numberTemp is null then set @numberTemp = 0;
	end if;
	set @number = @numberTemp;
	if @moneyTemp is null then set @moneyTemp = 0;
	end if;
	set @money = @moneyTemp;
	if @balanceTemp is null then set @balanceTemp = 0;
	end if;
	set @balance = @balanceTemp;
	-- 修改
	set @sql1 = concat('update ',
			   @querytable,
			   ' set as_money = ? , as_balance = ?',
			   ' where acid = ? and ufid = ? and as_type = ?'
			   );
	set @balance = @balance + @money0 - @money;
	prepare stmt from @sql1;
	execute stmt using @money0,@balance,@acid,@ufid0,@zhifu0;
	deallocate prepare stmt;
	-- 更新
	set @sql1 = concat('SELECT MAX(number) into @maxidTemp FROM ',@querytable,' where ufid = ? and as_type = ?;');
	prepare stmt from @sql1;
	execute stmt using @ufid0,@zhifu0;
	deallocate prepare stmt;
	if @maxidTemp is null then set @maxidTemp = 0;
	end if;
	set @maxid = @maxidTemp;
	set @numberPointer = @number + 1;
	while @numberPointer <= @maxid do
		set @sql1 = concat('select assetid,as_balance into @assetidTemp,@laterbalanceTemp from ',
				@querytable,
				' where number = ? and ufid = ? and as_type = ?;'
				);
		prepare stmt from @sql1;
		execute stmt using @numberPointer,@ufid0,@zhifu0;
		deallocate prepare stmt;
		if @assetidTemp is null then set @assetidTemp = 0;
		end if;
		set @assetid = @assetidTemp;
		if @laterbalanceTemp is null then set @laterbalanceTemp = 0;
		end if;
		set @laterbalance = @laterbalanceTemp;
		set @numberPointer = @numberPointer + 1;
		set @laterbalance = @laterbalance + @money0 - @money;

		set @sql1 = concat('update ',
				@querytable,
				' set as_balance = ? where assetid = ? and ufid = ? and as_type = ?'
				);
		prepare stmt from @sql1;
		execute stmt using @laterbalance,@assetid,@ufid0,@zhifu0;
		deallocate prepare stmt;
	end while;
	end if;
end if;

-- 刷新
-- 调用proc_asset_install()来重新读取jizhang_account中的数据，更新或新建各
-- 分类记录表，达到刷新的目的
if(func = 3)
then
	call proc_asset_install(@ufid0);
end if;

end if;

END
$
DELIMITER ;