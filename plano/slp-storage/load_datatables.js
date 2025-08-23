$(document).ready(function(){
			var GridView 	= $('#GridView').DataTable //Tampung data Ke Datatables
			({
				 "language"	: 	{
									"search"		: "Cari",
									"lengthMenu"	: "_MENU_ Baris per halaman",
									"zeroRecords"	: "Data tidak ada",
									"info"			: "Halaman _PAGE_ dari _PAGES_ halaman",
									"infoEmpty"		: "Data tidak ada",
									"infoFiltered"	: "(Filter dari _MAX_ data)",
									"paginate"		: 	{
														  "previous": "<",
														  "next": ">"
														}
								},
				
				lengthChange: true,
				lengthMenu	: [ 5, 10, 25, 50, 75, 100 ],
				paging		: true,
				 
				rowReorder	: 	{
									selector: 'td:nth-child(2)'
								},
				
				responsive	: true,
				colReorder	: true,
				fixedColumns: false,
				fixedHeader	: true,
				select		: true,
				
				buttons		: 	[
									{ extend: 'copy', className: 'btn-light', text: 'Copy' },
									{ extend: 'excel', className: 'btn-light' },
									{ extend: 'colvis', className: 'btn-light', text: 'Sembunyikan' }
								]
				
			});
		 
			GridView.buttons().container()
			
			.appendTo( '#GridView_wrapper .col-md-6:eq(0)' );
			
			$('#GridView').show(); //Tampilkan Tabel setelah selesai load semua dari Query
			
			GridView.columns.adjust().draw();
			
			$("#load").fadeOut(); //Hilangkan loading saat load halaman selesai
		});