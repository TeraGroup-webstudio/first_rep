window.addEventListener('DOMContentLoaded', (event) => {

  window.addEventListener('scroll', function(e) {
    window.scrollY > 30 ? document.body.classList.add('sticky_header') : document.body.classList.remove('sticky_header');
  });

  let showCategories = document.getElementById('show_categories');
  let categoriesListWraper = document.getElementById('categories_listWraper');
  let closeCategories = document.getElementById('categories_listButtonClose');
  let subcategoryButtonClose = document.querySelectorAll('.subcategoryButtonClose');
  let section = 0;
  showCategories.addEventListener('click', function(event){
    event.preventDefault()
    let headerHeight = document.querySelector('header').offsetHeight;
    let section_tmp = document.querySelector('.section-information');

    if(section_tmp !== null){
      section = section_tmp.offsetHeight;

    }

    console.log(headerHeight + section);
    //categoriesListWraper.style.cssText = `top: ${(headerHeight + section)}px;height: calc(100% - ${(headerHeight + section)}px)`;
    categoriesListWraper.style.cssText = `top: ${(headerHeight + section)}px;`;
    toggleMenu()
  });
  
  closeCategories.addEventListener('click', function(event){
    toggleMenu()
  });

  for(let i = 0; i< subcategoryButtonClose.length; i++){
    subcategoryButtonClose[i].addEventListener('click', function(event){
    this.parentElement.parentElement.classList.remove('open')
  });
  }

  categoriesListWraper.addEventListener('click', function(event){
    if (event.target === categoriesListWraper) {
    toggleMenu()
    }
  });
  
  function toggleMenu() {
    document.body.classList.toggle('lock');
    categoriesListWraper.classList.toggle('open');
    showCategories.classList.toggle('open');
  }

  let prevCategory = null;

  let lvlOpen = document.querySelectorAll('.categories_lvl1Box');
  let lvltwoOpen = document.querySelectorAll('.categories_lvl2Box');
  if(lvlOpen){
    for(let i = 0; i < lvlOpen.length; i++){
      lvlOpen[i].addEventListener('mouseover', function(event){
        event.preventDefault();
        // lvlOpen[i].firstElementChild.addEventListener('click', function(event){
        //   newHref = lvlOpen[i].firstElementChild.href;
        //   window.location.href = newHref;
        // }, false)
        setTimeout(() => {
        if(prevCategory){
          prevCategory.classList.remove('open');
        }
        prevCategory = this.parentElement;
        
        prevCategory.classList.add('open');
    }, 600)
      }, false);
    }
  }
});