<div class="adminBlock">
                        <div class="blockContainer">
                            <ul>
                                <li class="listView">
                                    <span class="impoData">Total Students</span>
                                    <span class="dataView"><?php echo $stu_count; ?></span>
                                </li>
                                <li>
                                    <span>Active Students</span>
                                    <span><?php echo $stu_count; ?></span>
                                </li>
                                <li>
                                    <span>Drop Students</span>
                                    <span><?php echo $stu_drop; ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="adminBlock">
                        <div class="blockContainer">
                            <ul>
                                <li class="listView">
                                    <span class="impoData">Fees Today</span>
                                    <span class="dataView">₹  
                                        <?php if($fees_today['deposite_amt']){
                                          
                                          echo $fees_today['deposite_amt'];

                                        }else{
                                            echo "0";
                                        }

                                    ?></span>
                                </li>
                                <li>
                                    <span>This Week</span>
                                    <span>₹<?php if($fees_count_week['deposite_amt']){
                                          
                                          echo $fees_count_week['deposite_amt'];
 
                                         }else{
                                             echo "0";
                                         }
                                     ?></span>
                                </li>
                                <li>
                                    <span>This Month</span>
                                    <span>₹
                                    <?php if($fees_count_month['deposite_amt']){
                                          
                                          echo $fees_count_month['deposite_amt'];

                                        }else{
                                            echo "0";
                                        }
                                    ?>
                                </span>
                                </li>
                                <li>
                                    <span>Total Year</span>
                                    <span>₹ <?php ;?>
                                    <?php if($fees_count_totalyear['deposite_amt']){
                                          
                                          echo $fees_count_totalyear['deposite_amt'];

                                        }else{
                                            echo "0";
                                        }
                                    ?>
                                   </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- <div class="adminBlock">
                        <div class="blockContainer">
                            <ul>
                                <li class="listView">
                                    <span class="impoData">New Admission</span>
                                    <span class="dataView"><?php //echo $new_admission; ?></span>
                                </li>
                            </ul>
                        </div>
                    </div> -->