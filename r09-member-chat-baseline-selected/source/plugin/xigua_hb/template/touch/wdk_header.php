<head>
  <style>
  .header {
      height: 80px;
      background-color: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px); /* 这是关键属性，用于模糊背景 */
 
    box-shadow: 0 -1px 8px 0 rgba(0,0,0,0.1);
      color: #000;
      transition: top 0.3s ease;
      position: fixed;
      top: -85px;
      width: 100%;
      z-index: 9999;
    }

  .header.show {
      top: 0;
    }

  .header em {
      position: absolute;
      top: 35px;
      left: 50%;
      font-size: 20px;
      font-weight: 600;
      transform: translateX(-50%);
    }
  </style>
</head>



<body>

<div class="header">
       <div style="margin-left:15px;margin-top:35px;"> <a href="javascript:window.history.go(-1);"><img style="width:20px;height:20px;margin-top:6px;" src="https://img.imehui.com/20250201/1738394711679dcc57cdb27.png" /></a>  </div> 
    <em>商合</em>
    </div>

  <script>
    window.addEventListener('scroll', function () {
      var header = document.querySelector('.header');
      if (window.pageYOffset > 0) {
        header.classList.add('show');
      } else {
        header.classList.remove('show');
      }
    });
  </script>
</body>
