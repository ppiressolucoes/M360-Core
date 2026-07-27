(function(){
  function find(root,selector){
    var scope=root&&root.querySelectorAll?root:document;
    var result=Array.prototype.slice.call(scope.querySelectorAll(selector));
    if(scope.matches&&scope.matches(selector)){result.unshift(scope);}
    return result;
  }

  function initializeNewsroom(root){
    find(root,'[data-m360-editorial-carousel]').forEach(function(carousel){
      if(carousel.getAttribute('data-m360-ready')==='true'){return;}
      carousel.setAttribute('data-m360-ready','true');
      var slides=Array.prototype.slice.call(carousel.querySelectorAll('[data-m360-editorial-slide]'));
      if(slides.length<2){return;}
      var previous=carousel.querySelector('[data-m360-editorial-prev]');
      var next=carousel.querySelector('[data-m360-editorial-next]');
      var current=0;
      var timer=null;
      var interval=Math.max(2500,parseInt(carousel.getAttribute('data-interval')||'6500',10));
      var autoplay=carousel.getAttribute('data-autoplay')==='true'&&!window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      function show(index){slides[current].hidden=true;slides[current].setAttribute('aria-hidden','true');current=(index+slides.length)%slides.length;slides[current].hidden=false;slides[current].setAttribute('aria-hidden','false');}
      function stop(){if(timer!==null){window.clearInterval(timer);timer=null;}}
      function start(){stop();if(autoplay){timer=window.setInterval(function(){show(current+1);},interval);}}
      if(previous){previous.addEventListener('click',function(){show(current-1);start();});}
      if(next){next.addEventListener('click',function(){show(current+1);start();});}
      carousel.addEventListener('mouseenter',stop);
      carousel.addEventListener('mouseleave',start);
      carousel.addEventListener('focusin',stop);
      carousel.addEventListener('focusout',start);
      document.addEventListener('visibilitychange',function(){if(document.hidden){stop();}else{start();}});
      start();
    });
  }

  function initializeWidgetCarousel(root){
    find(root,'[data-m360-widget-carousel]').forEach(function(carousel){
      if(carousel.getAttribute('data-m360-widget-ready')==='true'){return;}
      carousel.setAttribute('data-m360-widget-ready','true');
      var viewport=carousel.querySelector('.m360-editorial-widget__viewport');
      var track=carousel.querySelector('.m360-editorial-widget__track');
      var items=track?Array.prototype.slice.call(track.children):[];
      var previous=carousel.querySelector('[data-m360-widget-prev]');
      var next=carousel.querySelector('[data-m360-widget-next]');
      var status=carousel.querySelector('[data-m360-widget-status]');
      if(!viewport||!track||items.length<2){return;}
      var current=0;
      var visible=6;
      var timer=null;
      var interval=Math.max(2500,parseInt(carousel.getAttribute('data-interval')||'6500',10));
      var autoplay=carousel.getAttribute('data-autoplay')==='true'&&!window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      function measure(){var width=viewport.getBoundingClientRect().width;visible=width<=480?1:(width<=700?2:(width<=1000?3:6));current=Math.min(current,Math.max(0,items.length-visible));render();}
      function render(){var max=Math.max(0,items.length-visible);current=Math.max(0,Math.min(current,max));var offset=items[current]?items[current].offsetLeft:0;track.style.transform='translate3d(-'+offset+'px,0,0)';items.forEach(function(item,index){item.setAttribute('aria-hidden',index<current||index>=current+visible?'true':'false');});if(status){status.textContent=(current+1)+'–'+Math.min(items.length,current+visible)+' / '+items.length;}}
      function move(step){var max=Math.max(0,items.length-visible);current=step>0?(current>=max?0:current+1):(current<=0?max:current-1);render();start();}
      function stop(){if(timer!==null){window.clearInterval(timer);timer=null;}}
      function start(){stop();if(autoplay){timer=window.setInterval(function(){move(1);},interval);}}
      if(previous){previous.addEventListener('click',function(){move(-1);});}
      if(next){next.addEventListener('click',function(){move(1);});}
      carousel.addEventListener('mouseenter',stop);
      carousel.addEventListener('mouseleave',start);
      carousel.addEventListener('focusin',stop);
      carousel.addEventListener('focusout',start);
      document.addEventListener('visibilitychange',function(){if(document.hidden){stop();}else{start();}});
      if(window.ResizeObserver){new window.ResizeObserver(measure).observe(viewport);}else{window.addEventListener('resize',measure);}
      measure();
      start();
    });
  }

  function initializeTicker(root){
    find(root,'[data-m360-editorial-ticker]').forEach(function(ticker){
      if(ticker.getAttribute('data-m360-ticker-ready')==='true'){return;}
      ticker.setAttribute('data-m360-ticker-ready','true');
      var slides=Array.prototype.slice.call(ticker.querySelectorAll('[data-m360-ticker-slide]'));
      var previous=ticker.querySelector('[data-m360-ticker-prev]');
      var next=ticker.querySelector('[data-m360-ticker-next]');
      var toggle=ticker.querySelector('[data-m360-ticker-toggle]');
      if(slides.length<2){return;}
      var current=0;
      var timer=null;
      var interval=Math.max(2500,parseInt(ticker.getAttribute('data-interval')||'4500',10));
      var autoplay=ticker.getAttribute('data-autoplay')==='true';
      var respectReducedMotion=ticker.getAttribute('data-reduced-motion')==='respect';
      var userPaused=autoplay&&respectReducedMotion&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      function show(index){slides[current].hidden=true;slides[current].setAttribute('aria-hidden','true');current=(index+slides.length)%slides.length;slides[current].hidden=false;slides[current].setAttribute('aria-hidden','false');}
      function stop(){if(timer!==null){window.clearInterval(timer);timer=null;}}
      function updateToggle(){if(!toggle){return;}var paused=userPaused||timer===null;var pauseIcon=toggle.querySelector('[data-m360-ticker-pause-icon]');var playIcon=toggle.querySelector('[data-m360-ticker-play-icon]');toggle.setAttribute('aria-pressed',paused?'true':'false');toggle.setAttribute('aria-label',toggle.getAttribute(paused?'data-play-label':'data-pause-label')||'');if(pauseIcon){pauseIcon.hidden=paused;}if(playIcon){playIcon.hidden=!paused;}}
      function start(){stop();if(autoplay&&!userPaused&&!document.hidden){timer=window.setInterval(function(){show(current+1);},interval);}updateToggle();}
      if(previous){previous.addEventListener('click',function(){show(current-1);start();});}
      if(next){next.addEventListener('click',function(){show(current+1);start();});}
      if(toggle){toggle.addEventListener('click',function(){userPaused=!userPaused;if(userPaused){stop();updateToggle();}else{start();}});}
      ticker.addEventListener('mouseenter',function(){stop();updateToggle();});
      ticker.addEventListener('mouseleave',start);
      ticker.addEventListener('focusin',function(){stop();updateToggle();});
      ticker.addEventListener('focusout',start);
      document.addEventListener('visibilitychange',function(){if(document.hidden){stop();updateToggle();}else{start();}});
      start();
    });
  }

  function initialize(root){initializeTicker(root);initializeNewsroom(root);initializeWidgetCarousel(root);}
  function observe(){
    if(!window.MutationObserver||!document.body){return;}
    new window.MutationObserver(function(mutations){
      mutations.forEach(function(mutation){
        Array.prototype.forEach.call(mutation.addedNodes,function(node){if(node&&node.nodeType===1){initialize(node);}});
      });
    }).observe(document.body,{childList:true,subtree:true});
  }
  function elementor(){
    if(!window.elementorFrontend||!window.elementorFrontend.hooks){return;}
    window.elementorFrontend.hooks.addAction('frontend/element_ready/shortcode.default',function(scope){initialize(scope&&scope[0]?scope[0]:document);});
  }
  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',function(){initialize(document);observe();});}else{initialize(document);observe();}
  window.addEventListener('elementor/frontend/init',elementor);
  if(window.elementorFrontend){elementor();}
}());
