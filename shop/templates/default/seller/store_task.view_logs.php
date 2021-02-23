<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<style>
    .ncac_div{width:780px; margin:0 auto}
    .ncac_div table{width:100%; margin: 0 auto; margin-top: 20px; margin-bottom: 20px; }
    .ncac_div table tr th{padding:6px 4px;}
    .search_div{text-align: center;padding: 20px 0px 10px 0px;}
    .search_div p{display: inline-block;}
</style>
</head>
<body>
<div class="eject_con">
      <div class="ncac_div">
          <?php
           if(is_array($output["log_list"])): ?>
              <table class="ncsc-default-table order" id="selectTable">
                  <tbody>
                      <tr>
					      <th style="width: 5%;">序号</th>
						  <th style="width: 65%;">操作内容</th>                          
                          <th style="width: 10%;">操作人</th>
						  <th style="width: 20%;">操作时间</th>
                      </tr>
                      <?php
                          foreach($output["log_list"] as $index=>$log){
                              echo "<tr>";
							  //序号
							  echo "<th style='text-align:center'>".($index+1)."</th>";
                              //<!--日志-->
                              echo "<th >".$log["remark"]."</th>";
                              //<!--操作人-->
                              echo "<th  >".$log["create_user"]."</th>";
                              
                              //<!--操作时间-->
							  echo "<th  >".$log["create_time"]."</th>";
                              echo "</tr>";
                          }
                      ?>
                  </tbody>
                  <tfoot>
                  <?php if (is_array($output['log_list']) and !empty($output['log_list'])) { ?>
                      <tr>
                          <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
                      </tr>
                  <?php } ?>
                  </tfoot>
              </table>
          <?php endif; ?>
      </div>