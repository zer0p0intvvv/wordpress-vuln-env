function doesFileExist(urlToFile) {
  var xhr = new XMLHttpRequest();
  xhr.open('HEAD', urlToFile, false);
  xhr.send();
   
  if (xhr.status == "404") {
      return false;
  } else {
      return true;
  }
}

function reloadRobotsTxt (callback = undefined) {

  document.getElementById('click5_sitemap_robots_txt_container').style.display = 'none';
  toggleLoader('loader_status_robots', true);
  getRequest(c5resturl.wpjson + 'click5_sitemap/API/print_robots_txt', (data) => {

      document.getElementById('click5_sitemap_robots_txt_container').innerHTML = data;
      document.getElementById('click5_sitemap_robots_txt_container').style.display = 'flex';
      toggleLoader('loader_status_robots', false);
      if (callback !== undefined) {
        callback();
      }

      Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
      };
        var robots_file = c5homeurl.home + '/robots.txt?t='+jQuery.now();
        if (doesFileExist(robots_file)) {
          jQuery.get(robots_file, function(data) {

            document.getElementById('click5_sitemap_robots_txt_container').innerHTML = '<a href="'+c5homeurl.home+'/robots.txt" target="_blank" rel="nofollow">'+c5homeurl.home+'/robots.txt</a><form><textarea name="file_text" rows="7" style="margin-top: 15px; resize: none;">'+data+'</textarea><button type="button" name="update_txt" style="width: 101px; margin-top: 20px" class="button button-primary">Save Changes</button></form>';

            document.getElementById('click5_sitemap_robots_txt_container').style.display = 'flex';
          }, 'text');
  
          toggleLoader('loader_status_robots', false);
    
        } else {
          document.getElementById('click5_sitemap_robots_txt_container').style.display = 'flex';
          toggleLoader('loader_status_robots', false);
          document.getElementById('click5_sitemap_robots_txt_container').innerHTML = '<button type="button" name="update_txt" style="width: 101px; margin-top: 20px" class="button button-primary">Save Changes</button>';
        }
      //}

  });

}

function reloadSitemapsLinks(callback = undefined) {
  document.querySelectorAll('#click5_sitemap_url_container a.click5_sitemap_urls').forEach(el => {
    el.remove();
  })
  toggleLoader('loader_status_sitemap', true);
  
  getRequest(c5resturl.wpjson + 'click5_sitemap/API/print_sitemap_urls', (data) => {
    toggleLoader('loader_status_sitemap', false);
    let parsedData = data;
    if (parsedData.length) {
      try {
        document.querySelector('p.sitemap_not_gen').remove();
      } catch (e) {
        
      }

      document.getElementById('click5_sitemap_url_container').innerHTML="";      
      parsedData.forEach(el => {
        document.getElementById('click5_sitemap_url_container').innerHTML += '<a href="' + el + '" style="display: block; width: 100%;" target="_blank" class="click5_sitemap_urls">' + el + '</a>';
      });
      if (callback !== undefined) {
        callback();
        var tags = document.getElementById('tags_exist').value;
        if(tags == "0"){
          document.getElementById("click5_sitemap_seo_xml_tags").disabled= true;
        }       
         
      }
    } else {
      document.getElementById('click5_sitemap_url_container').innerHTML = '<p class="sitemap_not_gen" style="width: 100%;">sitemap.xml not generated yet.</p>';
      var tags = document.getElementById('tags_exist').value;
      if(tags == "0"){
        document.getElementById("click5_sitemap_seo_xml_tags").disabled= true;
      }    
    }
  });
  location.reload();
}

const checkSetting = (settings, setting_name) => {
  let result = undefined
  settings.forEach(setting => {
    if (setting.name == setting_name) {
      result = setting.value;
      return;
    }
  });
  return result;
}

const reGenerateButton = () => {
  document.getElementById('click5-ajax-loader').style.display = 'inline-block';

      let settings = [];
      document.querySelectorAll('#ajaxable select').forEach(el => {
        settings.push({name: el.getAttribute('name'), value: el.value });
      });
      document.querySelectorAll('#ajaxable input[type="checkbox"]').forEach(el => {
        settings.push({ name: el.getAttribute('name'), value: el.checked });
      });

      postRequestJSON(c5resturl.wpjson + 'click5_sitemap/API/generate_manual', {options: settings}, (data) => {
        document.getElementById('click5-ajax-loader').style.display = 'none';
        let enabledXML = checkSetting(settings, "click5_sitemap_seo_sitemap_xml");
          

          if(data == "404" || data == "405" || data == "406" || data == "407") {
            if(data == "407") {
              jQuery('[name="robots_error"]').css( "display", "block" );
              jQuery('[name="xml_error"]').css( "display", "block" );
            }
            if(data == "405") {
              jQuery('[name="robots_error"]').css( "display", "block" );
            }
            if(data == "406") {
              jQuery('[name="xml_error"]').css( "display", "block" );
            }
            if(data == "404") {
              reloadSitemapsLinks(() => {
                click5_sitemap_notification('success', 'Sitemap XML updated.', 2000);
                jQuery('.click5_sitemap_options_wrapper input[type="checkbox"]').prop('disabled', false);
                jQuery('[name="robots_error"]').css( "display", "block" );
              });
              
            }
            
            
          } else {
            reloadSitemapsLinks(() => {
              click5_sitemap_notification('success', 'Sitemap XML updated.', 2000);
              jQuery('.click5_sitemap_options_wrapper input[type="checkbox"]').prop('disabled', false);
            });
            reloadRobotsTxt(() => {
              if (enabledXML) {
                click5_sitemap_notification('success', 'Sitemap XML updated.', 2000);
              }
              jQuery('.click5_sitemap_options_wrapper input[type="checkbox"]').prop('disabled', false);
            });
          }     
      })   
      //jQuery("#escape_id").val("OK");

      //console.log(jQuery("#escape_id").val());
      jQuery(window).unbind('beforeunload');
      //window.location.href = window.location.href;
      
}

(() => {
  document.addEventListener("DOMContentLoaded", function (event) {
    if (!hasParameter('&tab=seo')) {
      return;
    }

    toggleLoader('loader_results', true);
    toggleLoader('loader_blacklisted', true);

    let inputSearch = document.querySelector('#page_search');
    let selectType = document.querySelector('#page_type');
    let hiddenAllTypes = document.querySelector('#all_types');

    document.getElementById('btnClearBlacklist').addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (confirm('Are you sure you want delete all blacklisted pages?')) {
        getRequest(c5resturl.wpjson + 'click5_sitemap/API/get_seo_clear', (data) => {
          if (data == true) {
            document.querySelectorAll('#click5_sitemap_already_blacklisted > li').forEach(el => {
              el.remove();
            });
            loadBlacklist('seo');
            searchFunc(inputSearch, selectType, hiddenAllTypes, 'seo');
            this.style.display = 'none';
          } else {
            click5_sitemap_notification('error', 'Something went wrong.', 2000);
          }
        });
      }

    });

    loadBlacklist('seo');

    document.addEventListener('blacklist_updated', function(e) {
      reGenerateButton();
    })

    searchFunc(inputSearch, selectType, hiddenAllTypes, 'seo');
    selectType.addEventListener('change', function (e) {
      searchFunc(inputSearch, selectType, hiddenAllTypes, 'seo');
    });
    inputSearch.addEventListener('input', debounce(function (e) {
      searchFunc(inputSearch, selectType, hiddenAllTypes, 'seo');
    }, 300));

    document.getElementById('generate_btn').addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      reGenerateButton();   
     
    });

    document.querySelectorAll('#ajaxable select').forEach(el => {
      el.addEventListener('change', debounce(function(e) {
        reGenerateButton();        
      }, 500));
    });
    document.querySelectorAll('#ajaxable input[type="checkbox"]').forEach(el => {
      el.addEventListener('change', debounce(function(e) {
      }, 500));
    });
  });
})();