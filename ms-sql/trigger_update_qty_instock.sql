BEGIN
	SET NOCOUNT ON;
	
	-- 	jika tidak update qty nya gak usah di update ke mst
	if not update(picking_qty)
        return;
				
	-- jika tidak ada data yang istored nya tidak 0 maka langsung kembalikan
	if not exists (select 1 from inserted where is_stored <> 0)
        return;

	update m
	set
    m.qty_IN     = isnull(m.qty_IN,0) + c.qty_hitung,
    m.qty        = isnull(m.qty,0)    + c.qty_hitung,
    m.loc_cd     = case 
                     when c.any_loc is not null and c.qty_hitung > 0 then c.any_loc
                     ELSE m.loc_cd
                   end,
    m.updated_at = getdate()
	from material_mst as m
	join (
	    select 
	        gabungan.material_no,
            sum(gabungan.qty_mst) as qty_hitung,
            max(gabungan.loc_new)   as any_loc
		from (
            select 
                i.material_no,
                cast(i.picking_qty as int)      as qty_mst,
                NULLIF(i.locate,'')             as loc_new
            from inserted i
            where (i.locate <> 'ASSY' or i.locate is null)
            and i.is_stored > 0

            union all

            select
                d.material_no,
                -cast(d.picking_qty as int)     as qty_mst,
                NULL                            
            from deleted d
            where (d.locate <> 'ASSY' or d.locate is null)
            and d.is_stored > 0
		) gabungan
      group by gabungan.material_no
      HAVING sum(gabungan.qty_mst) <> 0        
) as c
  on c.material_no = m.matl_no;
END