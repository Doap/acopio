		jQuery(document).ready(function () {
			jQuery('a.gallery_colorbox').colorbox({ 
				slideshow: true,
								title: false,
								slideshowAuto:false,
								slideshowSpeed: 5000 ,
				slideshowStart: 'Jugar',
				slideshowStop :  'Pausa',
				current : 'Imagen {current} de {total}', 
				scalePhotos : true , 
				previous: 'Anterior',	
				next:'Proxima',
				close:'Ciera',
				maxWidth: 640, 
				maxHeight : 900,
				opacity:0.8 , 
				onComplete : function(){ 
					jQuery("#cboxLoadedContent").css({overflow:'hidden'});
								},
				rel:'group1' 
			});
		});	
						
		