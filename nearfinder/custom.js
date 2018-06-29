var markers;
var pageOptions;
var adblock1;
var selectpickersocial;

var cities = new Bloodhound({
                              datumTokenizer: Bloodhound.tokenizers.whitespace,
                              queryTokenizer: Bloodhound.tokenizers.whitespace,
                              prefetch: '/funcoes/citiesjson.js'
                            });

var categoriestext = new Bloodhound({
                                      datumTokenizer: Bloodhound.tokenizers.whitespace,
                                      queryTokenizer: Bloodhound.tokenizers.whitespace,
                                      prefetch: {
                                        url: '/funcoes/categoriestextjson.js',
                                      }
                                    });

/*sobrponto o seletor para ficar case insensitive*/
jQuery.expr[':'].Contains = function(a, i, m) {
  return jQuery(a).text().toUpperCase()
      .indexOf(m[2].toUpperCase()) >= 0;
};

jQuery( document ).ready( function() {

  
/*
  $('.div_fixed_map2').css('height', $('.results').outerHeight(true) + 'px'); 
  */

  //$('.div_fixed_map2').scrollToFixed();
  /*
  $('.div_fixed_map2').affix(); 
  $('.div_fixed_map2').on('affix-top.bs.affix', function(){
    //alert('antes do top')
        //$('.div_fixed_map2').addClass('col-md-12');
        $('.div_fixed_map2').removeClass('col-md-3');
  });
  $('.div_fixed_map2').on('affix-bottom.bs.affix', function(){
    //alert('antes do bottom')
        //$('.div_fixed_map2').addClass('col-md-12');
        $('.div_fixed_map2').removeClass('col-md-3');
  });

  $('.div_fixed_map2').on('affix.bs.affix', function(){
    //alert('antes do bottom')
        $('.div_fixed_map2').addClass('col-md-3');
        //$('.div_fixed_map2').removeClass('col-md-12');
  });
  */

  

  

  $('.mobile-social-share a').click(function(event) {
      if ( $(this).attr('href') != '' ) {
        event.preventDefault();
        var myWindow = window.open($(this).attr('href'), "Share", "width=600,height=600");
        return false;
      }
    });
    
    $('.selectpicker').selectpicker();

        $('.payment-methods img').click(function(  ) {

          $('#metodo_' + $(this).data('rel')).attr('checked', !$('#metodo_' + $(this).data('rel')).is(":checked"));
            
        });
    
        $(".play_video").fancybox({
					maxWidth	: 800,
					maxHeight	: 600,
					fitToView	: false,
					width		: '70%',
					height		: '70%',
					autoSize	: false,
					closeClick	: false,
					openEffect	: 'elastic',
					closeEffect	: 'none'
				});
		
        try { new google.ads.search.Ads(pageOptions, adblock1); } catch ( e ) {} 
        
        $("#result").scroll(function () {
            var $this = $(this);
            var height = this.scrollHeight - $this.height(); // Get the height of the div
            var scroll = $this.scrollTop(); // Get the vertical scroll position

            var isScrolledToEnd = (scroll >= height);


            if (isScrolledToEnd && bln_req == false) {
                GetMoreContent();
            }
        });
        
        try { showMap(); } catch(e) { }
		
		$('.search-contain input[type="text"]').keyup(function(){
			var searchText = $(this).val();
			
			/*
			$('ul.search-list-see-all > li:not(:contains(' + searchText + '))', $(this).parent().parent()).hide(); 
			$('ul.search-list-see-all > li:contains(' + searchText + ')', $(this).parent().parent()).show();
			*/
			
			$('ul.search-list-see-all > li', $(this).parent().parent()).each(function(){
				var currentLiText = $(this).text();
				var showCurrentLi = currentLiText.toLowerCase().indexOf(searchText.toLowerCase()) > -1;
				//$(this).toggle(showCurrentLi);
				if ( showCurrentLi ) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});  
		});
        
        $('.close-promo').click(function() {
            $.get('/funcoes/fechapromo');
        });
        
        $('[data-toggle="tooltip"]').tooltip(); 
        
        // verifica se o navegador tem suporte a geolocalização
        /*
        if ( typeof getgeo != 'undefined' ) {
            getGeo();
        }
        */
        
        
        $('.fancylink').fancybox({
                width: '50%',
                height: '50%'
        });
        
        if ( typeof open_fancy_promo != 'undefined' ) {
            setTimeout(function(){ openFancyboxCadastro() }, 10000);
        }
        
        if ( typeof open_fancy != 'undefined' ) {
            
            /*
            var d = new Date();
            var n = d.getSeconds();
            
            if ( n % 2  ) {
            */
                openFancyboxPesquisa();
            /*
            } else {
                openFancybox();
            }
            */
        }
        try { $("#input-id").rating(); } catch( e ) { }
        
        
        $('.btn-util').click( function() {
                
                var value = $(this).parent().find('input[type=hidden]').val();
                $.ajax({
                       url: '/empresa/util',
                       type: 'POST',
                       dataType: 'json',
                       data: {
                        num_empresa_id: $('#num_empresa_id_util').val(),
                        bln_util: value
                       }, 
                       beforeSend: function (){
                           $('.informacao_util').slideUp('fast');
                       },
                       success: function ( result ) {
                           $('#value-util-sim').html(result.percent_util + "%");
                           $('#value-util-nao').html(result.percent_nao_util + "%");
                           $('#avaliacao-msg').hide();
                           $('#avaliacao-numeros').removeClass('hidden');
                           $('#div-util-inutil').hide();
                       }
                });
                
        });
        
        $('.como_chegar_imprimir').click(function(){
                //$.print("#como-chegar-print");
                //$.print("#texto_rota");
                $('.logo-site-imprimir').show();
                $.print("#empresa_div_mapa_print");
                $('.logo-site-imprimir').hide();
        });
        
        $('#inverter-enderecos').click(function() {
			aux = $('#rota_de').val();
			$('#rota_de').val($('#rota_para').val());
			$('#rota_para').val(aux);
			return false;
        });
        
        
        /*Google Maps*/
        try{
            jQuery('#empresa_mapa').gmap({
                   zoom: 15,
                   scrollwheel:  false,
                   icon: {
                            image: "/public/default/images/marker.png", 
                            iconsize: [26, 46],
                            iconanchor: [12, 46],
                            infowindowanchor: [12, 0]
                   },
                   styles: map_style
               }).bind('init', function() {
                   jQuery('#empresa_mapa').gmap('search', 
                                          { 'address': endereco_completo },
                                          function(results, status) {
                                              console.log(status);
                                              
                                              if ( status === 'OK' ) {
                                                  var geoloc = results[0].geometry.location;    
                                                  if ( bln_atualiza_coord ) {
                                                      jQuery.ajax({
                                                          url: "/funcoes/atualizageolocalizacao",
                                                          type: 'POST',
                                                          data: "empresa_id="+num_id_como_chegar+"&location="+results[0].geometry.location,
                                                          async: false,
                                                          success: function(response){
                                                              console.log(response);
                                                          }
                                                      }); 
                                                  }
                                                  
                                                  jQuery('#empresa_mapa').gmap('get', 'map').panTo(results[0].geometry.location);
                                                  var marker = jQuery('#empresa_mapa').gmap('addMarker', { 'position': results[0].geometry.location } );
                                                  marker.click(function() {
                                                                jQuery('#empresa_mapa').gmap('openInfoWindow', { 'content': descricao_como_chegar }, this);
                                                        });
                                                  
                                                  jQuery('#empresa_mapa').gmap('openInfoWindow', { 'content': descricao_como_chegar }, marker);
                                              }
                                          });
            });
               
            var rota_de = document.getElementById('rota_de');

            (function pacSelectFirst(input) {
                // store the original event binding function
                var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;
        
                function addEventListenerWrapper(type, listener) {
                    // Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected,
                    // and then trigger the original listener.
                    if (type == "keydown") {
                        var orig_listener = listener;
                        listener = function(event) {
                            var suggestion_selected = $(".pac-item-selected").length > 0;
                            if (event.which == 13 && !suggestion_selected) {
                                var simulated_downarrow = $.Event("keydown", {
                                    keyCode: 40,
                                    which: 40
                                });
                                orig_listener.apply(input, [simulated_downarrow]);
                            }
        
                            orig_listener.apply(input, [event]);
                        };
                    }
        
                    _addEventListener.apply(input, [type, listener]);
                }
        
                input.addEventListener = addEventListenerWrapper;
                input.attachEvent = addEventListenerWrapper;
        
                var autocomplete = new google.maps.places.Autocomplete(input);
        
            })(rota_de);
            
            
            var rota_para = document.getElementById('rota_para');

            (function pacSelectFirst(input) {
                // store the original event binding function
                var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;
        
                function addEventListenerWrapper(type, listener) {
                    // Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected,
                    // and then trigger the original listener.
                    if (type == "keydown") {
                        var orig_listener = listener;
                        listener = function(event) {
                            var suggestion_selected = $(".pac-item-selected").length > 0;
                            if (event.which == 13 && !suggestion_selected) {
                                var simulated_downarrow = $.Event("keydown", {
                                    keyCode: 40,
                                    which: 40
                                });
                                orig_listener.apply(input, [simulated_downarrow]);
                            }
        
                            orig_listener.apply(input, [event]);
                        };
                    }
        
                    _addEventListener.apply(input, [type, listener]);
                }
        
                input.addEventListener = addEventListenerWrapper;
                input.attachEvent = addEventListenerWrapper;
        
                var autocomplete = new google.maps.places.Autocomplete(input);
        
            })(rota_para);
               
        } catch (e) {
            console.log('Map error', e.message);
        }
        
        $('.mapa .funny-boxes').hover(function() {
            if ( !$(this).hasClass('publicidade') ) {
                addEventMap( this );
            }
        })
        
        $('#btn_traca_rota').click(function() {
            try {
                ga('send', 'event', 'formularios', 'Traçar rota', 'Traçar')
            } catch(e) {}
            
            if ( $('#rota_de').val() == "" ) {
                alert(lang.informar_endereco_origem);
                $('#rota_de').focus();
                return false;
            }
            
            setCookie("rota_de",$('#rota_de').val(),365);
            setCookie("rota_para",$('#rota_para').val(),365);
            
            $('#texto_rota').html('');
            $('#texto_rota_loading').show('fast');
            
            mapa = $('#map_canvas').length ? $('#map_canvas') : $('#empresa_mapa');
            //console.log(mapa);
            
            mapa.gmap('displayDirections', {
                        'origin': $('#rota_de').val(),
                        'destination': $('#rota_para').val(),
                        'travelMode': eval('google.maps.DirectionsTravelMode.' + $('input[name=tipo_gps]:checked').val())
                    }, {
                        'panel': document.getElementById('texto_rota')
                    }, function(result, status) {
                      console.log(result)
                           if ( status !== 'OK' ) {
                                   alert(lang.nenhum_resultado_encontrado);
                           }
                           
                    });
            $('#texto_rota_loading').hide();
            return false;
        });
        
        
        //alert(window.location.href )
        //alert(window.location.host)
        //if ( window.location.href != 'http://' + window.location.host + '/' ) {
        //alert(location.pathname)
        //var is_root = location.pathname == "/";
        if ( typeof show_topbar != 'undefined' && show_topbar && findBootstrapEnvironment() != 'xs' ) {
            try {
                var top = $('#cabecalho').offset().top;
                $(window).scroll(function (event) {
                               var y = $(this).scrollTop();
                               if(y>=top + 15){
                                              $('#cabecalho').addClass('menu_fixed');
                                              //$('#sombra').addClass('fixed2');
                                              //$('#sombra').fadeIn('slow');
                               }else{
                                              $('#cabecalho').removeClass('menu_fixed');
                               }
                });
            } catch( e ) {}
        }

        
        $('.help_chat .close').click(function() {
                $('.help_chat').fadeOut();
        })
        
        setTimeout(function(){$('.help_chat').css('display','block')},3000);
        
        $("#arquivo_foto").fileinput({
            previewFileType: "image",
            browseClass: "btn btn-info rounded",
            browseLabel: " " + lang.selecionar_imagem, 
            browseIcon: '<i class="glyphicon glyphicon-picture"></i>',
            removeClass: "btn btn-danger",
            removeLabel: " " + lang.deletar,
            removeIcon: '<i class="glyphicon glyphicon-trash"></i>',
            uploadClass: "btn btn-success color-white",
            uploadLabel: " " + lang.salvar,
            uploadIcon: '<i class="glyphicon glyphicon-upload"></i>',
            uploadUrl: "/usuario/alteraimagem?str_acao=alterar imagem",
            allowedFileExtensions: ["jpg"],
            initialPreview: [
                    "<img src='" + $('#img_preview_user').attr('src') + "' class='file-preview-image'>",
                ],
            dropZoneEnabled: false
        });
        
        $('#arquivo_foto').on('filebatchuploadcomplete', function(event, files, extra) {
            window.location = window.location.href.substr(0, window.location.href.indexOf('#')) + "?sucesso=1";
        });
        
        
        $('.str_cep_checkout').mask("99999999");
        $('.dte_mask').mask("99/99/9999");
        
        $('.sky-form-cad-aux').submit(function() {
                $('.sky-form-cad').submit();
                return false;
        });

        var categories = new Bloodhound({
          datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          prefetch: '/funcoes/categoriesjson.js'
        });
        categories.initialize();

        var elt = $('.input_categoria_cad');
        elt.tagsinput({
          itemValue: 'value',
          itemText: 'text',
          maxTags: 7,
          tagClass: function(item) {
            return 'label label-blue rounded';
          },
          typeaheadjs: {
            highlight: true,
            minLength: 1,
            hint: true,
            name: 'cities',
            displayKey: 'text_number',
            source: categories.ttAdapter(),
            freeInput: true,
            limit: 10
          }
        })

        if ( typeof rr_cats_aux != 'undefined' )  {
          for( i in rr_cats_aux ) {
            elt.tagsinput('add', rr_cats_aux[i]);
          }
        }
        
        $("#bln_mostramapa").click(function(){
            if ( $(this).is(":checked") ) {
                $("#bln_mostraendereco").attr("checked", "checked");
            }
        });
        
        $("#bln_mostraendereco").click(function(){
            if ( !$(this).is(":checked") ) {
                $("#bln_mostramapa").removeAttr("checked");
            }
        });
        
        $('.add_exped').click(function() {
            var obj = $('.row_expediente:last').parent().clone(true);
            $('.hidden', $('.row_expediente:last')).removeClass('hidden');
            $('.add_exped', $('.row_expediente:last')).remove();
            //$('.hidden', $('.row_telefone:last')).show()
            
            //$('.row_telefone .add_telefone').parent().remove();
            obj.find('input, select').each(function(  ) {
                $(this).val('');
            });
            
            obj.insertBefore( $(".row_expediente_add").parent() );
        })
        
        $('.add_telefone').click(function() {
            var obj = $('.row_telefone:last').parent().clone(true);
            $('.hidden', $('.row_telefone:last')).removeClass('hidden');
            $('.add_telefone', $('.row_telefone:last')).remove();
            //$('.hidden', $('.row_telefone:last')).show()
            
            //$('.row_telefone .add_telefone').parent().remove();
            obj.find('.col-md-6 input').each(function(  ) {
                $(this).val('');
            });

            $('.selectpicker', obj).data('selectpicker', null)
            $('.bootstrap-select', obj).remove();
            $('.selectpicker', obj).selectpicker();
            
            obj.insertBefore( $(".row_telefone_add").parent() );
        })
        
        $('.add_social').click(function() {
            var obj = $('.row_social:last').parent().clone(true);
            //$('.bootstrap-select', obj).remove();
            //obj.find('.bootstrap-select').replaceWith(function() { return $('select', this); })
            //obj.find('select').each(function() { $(this).css('display','') });

            //console.log(obj.html());
            $('.hidden', $('.row_social:last')).removeClass('hidden');
            $('.add_social', $('.row_social:last')).remove();
            //$('.hidden', $('.row_telefone:last')).show()
            
            //$('.row_telefone .add_telefone').parent().remove();
            obj.find('.col-md-6 input').each(function(  ) {
                $(this).val('');
            });

            //$('.input-social',obj).html('');
            //console.log(selectpickersocial.html())
            //$('.input-social',obj).html(selectpickersocial);
            //$('.selectpicker', obj).selectpicker().selectpicker('show');

            $('.selectpicker', obj).data('selectpicker', null)
            $('.bootstrap-select', obj).remove();
            $('.selectpicker', obj).selectpicker();

            //$('.selectpicker',$('.row_social:last')).selectpicker('destroy');



            //$('.selectpicker',$('.row_social:last')).selectpicker('destroy');
            //selectpickersocial

            //$('.selectpicker', obj).selectpicker();
            //$('.selectpicker',$('.row_social:last')).selectpicker('destroy');
            //$('.selectpicker',$('.row_social:last')).selectpicker();
            //console.log(obj.html());
            //console.log("asdasdasd");
            obj.insertBefore( $(".row_social_add").parent() );
        })

        $('.del_social, .del_telefone').click(function() {
            $(this).parent().parent().parent().remove();
        })
        
        MaskCEP();
        
        $('#num_estado_id').change(function(){
            $('#num_cidade_id').attr('disabled', 'disabled');
            $('#num_cidade_id').html('<option value="">' + lang.carregando + '</option>');
            $('#num_bairro_id').html('<option value="">' + lang.escolha_bairro + '</option>');
            $.get("/empresa/retornacidades/id/" + $(this).val(), null,
                                    function(response){
                                        $('#num_cidade_id').append( response );
                                        $('#num_cidade_id' + ' option:first').html(lang.escolha_cidade);
                                        $('#num_cidade_id').removeAttr('disabled');
                                    }
            );
        });

        $('#num_cidade_id').change(function(){
            $('#num_bairro_id').attr('disabled', 'disabled');
            $('#num_bairro_id').html('<option value="">' + lang.carregando + '</option>');
            $.get("/empresa/retornabairros/id/" + $(this).val(), null,
                                    function(response){
                                        $('#num_bairro_id').append( response );
                                        $('#num_bairro_id option:first').html(lang.escolha_bairro);
                                        $('#num_bairro_id').removeAttr('disabled');
                                    }
            );
        });
        
        $('input, textarea', $('.state-error')).focus(function() {
                element = $(this).parent().parent();
                $('em.invalid', element).remove();
                $('label.state-error', element).removeClass('state-error');
        })



    $('.typeahead_oque').typeahead({
        minLength: 0
    }, {
      source: categoriestextFunction,
      name: 'categories'
    });

/*
    typeaheadjs: {
        highlight: true,
        minLength: 1,
        hint: true,
        name: 'cities',
        displayKey: 'text_number',
        source: cities.ttAdapter(),
        freeInput: true,
        limit: 10
      }
      */
    
    $('.typeahead_onde, #str_ddd').typeahead({
        minLength: 0
    },

//<button type="button" id="btn-enable-location" data-style="expand-right" class="ladda-button btn-u <?php echo (( isset( $_SESSION['str_onde'] ) && $_SESSION['str_onde'] == $this->translate->_('Localização atual') )?'':'btn-u-default'); ?> glow" data-toggle="tooltip" data-placement="top" title="<?php echo $this->translate->_('Localização atual'); ?>"><i class="fa fa-location-arrow" aria-hidden="true"></i></button>
     {
      source: citiesFunction,
      name: 'cities',
      templates: {
        header : [
          '<div class="tt-suggestion tt-suggestion-location tt-selectable" id="btn-enable-location"><i class="fa fa-location-arrow" aria-hidden="true"></i> ' + lang.localizacao_atual + '</div>',
        ].join('\n')
      }
    }
    );


    

        var elt2 = $('.rr_cidades_cobertura_id');
        elt2.tagsinput({
          maxTags: 10,
          tagClass: function(item) {
            return 'label label-blue rounded';
          },
          typeaheadjs: {
            highlight: true,
            minLength: 1,
            hint: true,
            name: 'cities',
            source: cities.ttAdapter(),
            freeInput: true,
            limit: 10
          }
        });

        if ( typeof rr_cidades_cobertura_cities != 'undefined' )  {
          for( i in rr_cidades_cobertura_cities ) {
            elt2.tagsinput('add', rr_cidades_cobertura_cities[i]);
          }
        }


        var elt3 = $('.str_palavras_chave');
        elt3.tagsinput({
          maxTags: 20
        });

        if ( typeof rr_palavras != 'undefined' )  {
          for( i in rr_palavras ) {
            elt3.tagsinput('add', rr_palavras[i]);
          }
        }

    /*
    var rr_cidades_cobertura_id = jQuery(".rr_cidades_cobertura_id").tagsManager({
          prefilled: (( typeof rr_cidades_cobertura_ids != 'undefined' )?rr_cidades_cobertura_ids:null),
          maxTags: (( typeof num_cidades_allowed != 'undefined' )?num_cidades_allowed:5),
          tagClass: 'btn-u text-white',
          onlyTagList: true
      }).typeahead({
          source: function (query, process) {
              return $.getJSON(
                  '/funcoes/retornacidadesautocomplete',
                  { q: query },
                  function (data) {
                      return process(data);
                  });
          },
          afterSelect: function(tag) {
              rr_cidades_cobertura_id.tagsManager("pushTag", tag);
          }
      });

      var rr_palavras_chave = jQuery(".str_palavras_chave").tagsManager({
          prefilled: (( typeof rr_palavras != 'undefined' )?rr_palavras:null),
          maxTags: 20,
          tagClass: 'btn-u text-white'
      })
*/
    /*
    var cidades = new Bloodhound({
          datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: {
            url: '/funcoes/retornacidadesautocomplete?q=%QUERY',
            wildcard: '%QUERY'
          }
        });
        
        $('.typeahead_onde, #str_ddd').typeahead(null, {
          name: 'typeahead_onde',
          display: 'value',
          source: cidades
        });
        */
    
    App.init();
    try{ OwlCarousel.initOwlCarousel(); } catch(e){}   
    try{ App.initCounter(); } catch(e){}
    try{ App.initParallaxBg(); } catch(e){}
    try{ StyleSwitcher.initStyleSwitcher(); } catch(e){}     
    try{ CirclesMaster.initCirclesMaster2(); } catch(e){}
    try{ FancyBox.initFancybox(); } catch(e){}
    try{ Business.initPanorama(); } catch(e){}
    try{ Business.initMap(); } catch(e){}
    try{ Datepicker.initDatepicker(); } catch(e){}
    
    try {
            if ( $('body').attr('id') == 'anuncie' ) {
                $.backstretch([
                  "/public/default/assets/img/bg/5.jpg",
                  "/public/default/assets/img/bg/4.jpg",
                  ], {
                    fade: 1000,
                    duration: 7000
                });
            }
    } catch(e) {}
    
    try {
                                $('.job-img').backstretch([
                                       //"/public/default/assets/img/sliders/layer/bg2.jpg",
                                       "/public/default/assets/img/sliders/layer/2.jpg",
                                       "/public/default/assets/img/sliders/layer/3.jpg",
                                       "/public/default/assets/img/sliders/layer/4.jpg",
                                       "/public/default/assets/img/sliders/layer/5.jpg",
                                       "/public/default/assets/img/sliders/layer/6.jpg",
                                       "/public/default/assets/img/sliders/layer/7.jpg",
                                       "/public/default/assets/img/sliders/layer/8.jpg",
                                       "/public/default/assets/img/sliders/layer/9.jpg",
                                       "/public/default/assets/img/sliders/layer/1.jpg"
                                       ], {
                                         fade: 1000,
                                         duration: 7000,
                                         centeredY: false
                                     });
            
    } catch(e) {}


      // Bind progress buttons and simulate loading progress
      $('body').on('click', '#btn-enable-location', function() {
            getGeo(true);
      } );
    
});

function MaskCEP(){
    
    if(is_en == false){
        $("#str_cep").mask("99999-999");
        $("input#str_cpf_responsavel").mask("999.999.999-99");
        $("input#str_cep2").mask("99999-999", {
                    completed: function(){
                        var html = ('<p>' + lang.carregando + '</p>');
		
                        $.colorbox({
                            html: html,
                            close: false,
                            escKey: false,
                            overlayClose: false,
                            fixed: true
                        });
                        $.ajax({
                            url: "/funcoes/buscacep/cep/" + $(this).val(),
                            data: ({
                            }),
                            dataType: 'json',
                            success: function(response){
                                if(unescape(response) != ""){
                                    $("select#str_logradouro").val(response.tipo_logradouro);
                                    $("input#str_endereco").val(response.logradouro);
                                    $("input#str_bairro").val(response.bairro);
                                    $("select#num_estado_id").val(response.estado_id);
                                    if ( response.cidade_id != "" ) {
                                        $("select#num_cidade_id option").remove();
                                        $("select#num_cidade_id").append('<option value="' + response.cidade_id + '">'+ response.cidade + '</option>');
                                        $("select#num_cidade_id").val(response.cidade_id);
                                    } else {
                                        $("select#num_estado_id").trigger('change');
                                    }

                                    if ( response.bairro_id != "" ) {
                                        $("select#num_bairro_id option").remove();
                                        $("select#num_bairro_id").append('<option value="">'+ lang.nenhum + '</option>');
                                        $("select#num_bairro_id").append('<option value="' + response.bairro_id + '">'+ response.bairro + '</option>');
                                        $("select#num_bairro_id").val(response.bairro_id);
                                    } else {
                                        $("select#num_cidade_id").trigger('change');
                                    }

                                    $.colorbox.close();
                                    //montaPreview();
                                }
                            }
                        });
                        /*$.ajax({
                            url: "/funcoes/buscacep/cep/" + $(this).val(),
                            data: ({
                            }),
                            async: false,
                            success: function(response){
                                if(unescape(response) != ""){
                                    $("div#wrapper select#str_logradouro").val(response.split("|")[1]);
                                    $("div#wrapper input#str_endereco").val(response.split("|")[2]);
                                    $("div#wrapper input#str_bairro").val(response.split("|")[3]);
                                    $("div#wrapper select#num_estado_id").val(response.split("|")[4]);
                                    if ( response.split("|")[5] != "" ) {
                                        $("div#wrapper select#num_cidade_id option").remove();
                                        $("div#wrapper select#num_cidade_id").append('<option value="' + response.split("|")[5] + '">'+ response.split("|")[6] + '</option>');
                                        $("div#wrapper select#num_cidade_id").val(response.split("|")[5]);
                                    } else {
                                        $("div#wrapper select#num_estado_id").trigger('change');
                                    }
                                }
                            }
                        });*/
                    }
            });
    } else {
        //$("input#str_cpf_responsavel").mask("999-99-9999");
        //$("input#str_cep2").mask('99999');
        //$("#str_cep").mask('99999');
    }
}

function exibeTelefone( num_empresa_id, str_slug, bln_publicidade ) {
  //console.log(bln_publicidade);
    try {
        //_gaq.push(['_trackEvent', 'formularios', 'Ver Telefone', str_slug]);
        ga('send', 'event', 'formularios', 'Ver Telefone',  str_slug)
    } catch(e) {}

    /*
    $.get('/empresa/telefonesdados/id/' + num_empresa_id, null, function( response ) {
              $('.strong-fone').html( response );
        })
    */
    
    
    $.get('/empresa/telefonesdados/id/' + num_empresa_id, { bln_sem_publicidade: bln_publicidade }, function( response ) {
          $('.strong-fone').html( response );
    })
    
    $('.span-ver-telefone').hide();
}



function setCookie(c_name,value,exdays) {
	value = encodeURIComponent(value);
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=value + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}

function getCookie(c_name) {
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++) {
        x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
        y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
        x=x.replace(/^\s+|\s+$/g,"");
        if ( x == c_name ) {
            return decodeURIComponent(y);
        }
    }
}

function openFancyboxPesquisa() {
    //alert('openFancyboxPesquisa')
    $.fancybox(
								{
								'title': 'Ajude-nos a melhorar',
								'transitionIn'      : 'none',
								'transitionOut'     : 'none',
								'scrolling'			: 'no',
								height: '500px',
                                width: '500px',
                                autoSize: false,
								content: '<iframe src="http://www.surveygizmo.com/s3/2084141/Pesquisa-GuiaJ" frameborder="0" width="500" height="700" style="overflow:hidden"></iframe>',
                                });
}

function openFancyboxCadastro() {
    //alert('openFancyboxPesquisa')
    $.fancybox(
								{
								'transitionIn'      : 'none',
								'transitionOut'     : 'none',
								'scrolling'			: 'no',
                                autoSize: true,
								content: '<iframe src="/funcoes/promo" frameborder="0" width="500" height="750" style="overflow:hidden" scrolling="no"></iframe>',
								'centerOnScroll'    : true,
								'autoScale'         : true,
								'autoDimensions'    : true,
								 autoCenter: true
                                });
}

function findBootstrapEnvironment() {
    var envs = ['xs', 'sm', 'md', 'lg'];

    var $el = $('<div>');
    $el.appendTo($('body'));

    for (var i = envs.length - 1; i >= 0; i--) {
        var env = envs[i];

        $el.addClass('hidden-'+env);
        if ($el.is(':hidden')) {
            $el.remove();
            return env;
        }
    }
}


function getGeo( bln_click ) {
    //if ( !bln_click && ( getCookie( 'bln_requested_geo' ) == 1 || getCookie( 'bln_requested_geo_denied' ) == 1 ) ) {
    //return;
    //}

    if ( navigator.geolocation ) {
        navigator.geolocation.getCurrentPosition(function(position){ // callback de sucesso
          $('.typeahead_onde').typeahead('close');
                $.ajax({
                  type: 'GET',
                  url: '/funcoes/geosave?lat=' + position.coords.latitude + "&lon=" + position.coords.longitude,
                  data: {
                        lat: position.coords.latitude,
                        lon: position.coords.longitude 
                    },
                  success: function( res ) {
                      $('input[name=str_onde]').val(res);
                      console.log('response: ' + res);
                  },
                  dataType: 'html'
                });
        }, 
        function(error){ // callback de erro
           //alert('Erro ao obter localização!');
           console.log('Erro ao obter localização.', error);
           //setCookie("bln_requested_geo_denied",1,1);
        });
    } else {
      //setCookie("bln_requested_geo",1,1);
      console.log('Navegador não suporta Geolocalização!');
    }
    
}

function getParameterByName(name, url) {
    if (!url) {
      url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}


function GetMoreContent() {
    //return "<p>This is the div content</p><p>This is the div content</p><p>This is the div content</p><p>This is the div content</p><p>This is the div content</p>";
    
    bln_req = true;
    
    if ( bln_fim == true ) {
        bln_req = false;
        return;
    }
    
    $("#result").append('<div class="text-center margin-bottom-20"><i class="fa fa-refresh fa-spin fa-3x fa-fw margin-bottom color-blue"></i></div>');
    $("#result").animate({ scrollTop: $('#result').prop("scrollHeight") -10});
    
    
    if ( typeof num_pagina == 'undefined' ) {
        num_pagina = getParameterByName('p', window.location.href);
            
        if ( !isNaN(num_pagina) ) {
            num_pagina = 0;
        }
    }
    
    num_pagina++;
    var str_url = window.location.href;
    //alert('pagina ' + num_pagina + " str_url = " + str_url);
    $.get(str_url, {map:1, p: num_pagina}, function( response ) {
        $('.fa-refresh', $("#result")).remove();
        $("#result").append(response);
        
        $('.mapa .funny-boxes').unbind('hover');
        $('.mapa .funny-boxes').hover(function() {
            addEventMap( this )
        })
        
        if ( bln_fim == true ) {
            $("#result").unbind('scroll');
        }
        
        bln_req = false;
    })
}

function addEventMap( obj ) {
    
    if ( $(obj).hasClass('publicidade') ) {
        return false;
    }
    
    $('.funny-boxes-selected').removeClass('funny-boxes-selected')
    $(obj).addClass('funny-boxes-selected');
    
    num_position = $('input[name=num_position]', $(obj)).val();
    console.log("Position " + num_position);
    //console.log(markers[num_position]);
    //console.log(markers[1000]);
    //console.log('marker_' + map_empresa_id + '.getPosition()');
    //google.maps.event.trigger(markers[num_position], 'click');
    
    if ( typeof markers[num_position] != 'undefined' ) {
        $('#map_canvas').gmap('openInfoWindow', { content: descriptions[num_position] }, markers[num_position]);
    }
}

function scroolToEmp( num_emp_id ) {
    
    $('.funny-boxes-selected').removeClass('funny-boxes-selected')
    $('#bus-' + num_emp_id).addClass('funny-boxes-selected');
    
    $parentDiv = $('#result'); 
    $innerListItem = $('#bus-' + num_emp_id)
    
    $parentDiv.animate({ scrollTop: ($parentDiv.scrollTop() + $innerListItem.position().top - $parentDiv.height()/2 + $innerListItem.height()/2) }, 'slow');
}

function citiesFunction(q, sync) {
  if (q === '') {
    sync(getDefaultWhereSearch(cities, sync));
  } else {
    cities.search(q, sync);
  }
}

function categoriestextFunction(q, sync) {
  if (q === '') {
    sync(getDefaultWhatSearch(categoriestext, sync));
  } else {
    categoriestext.search(q, sync);
  }
}