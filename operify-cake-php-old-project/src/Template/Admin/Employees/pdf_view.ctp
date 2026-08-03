<?php 
class xtcpdf extends TCPDF {
 
}
 
   $this->set('pdf', new TCPDF('L','mm','A4'));
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->AddPage();
$srch=$this->Session->read('search');

//pdf->SetY(-550);
$pdf->SetFont('', '', 9, '', 'false');
if($employees['experience']=='0') { $exp="Fresher"; }
else { $exp=$employees['experience']."year"; }
if($employees['status']=='Y'){ $status="<span class='label label-success'> Active</span>"; } else{ $status= "<span class='label label-primary'> In-Active</span>"; }

$html='
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>student-erp</title>
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet"> 
<style>
table, td, th{ border:1px solid #ccc; padding:5px; font-size:14px;}
th{ color:#900;}
tr:nth-child(even){background-color: #f2f2f2}


</style>



 </head>

<body>

<div id="pageContainer1" style="width:992px; margin:auto;background-color: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.4);
-moz-box-shadow: 0 0 10px rgba(0,0,0,0.4);
-webkit-box-shadow: 0 0 10px rgba(0,0,0,0.4);
-o-box-shadow: 0 0 10px rgba(0,0,0,0.4); padding:30px;">


<table width="100%" border="0" style="border:none">
<tr border="0" style="border:none">
<td border="0" class="lg" style=" width:10%; border:none"><img src="images/L_58839.gif" alt="logo"></td>

<td border="0" style=" width:90%; font-size:17px; border:none"><strong style=" margin:6px 0; color:#5d5a50;">iDS PRIME</strong><br>
<small style="color:#5d5a50; font-weight:400;">

A3 Mall Road Near Radhey Bakers,
Vidhyadhar Nagar, Jaipur 
Pincode: 302039
+91-0141-3222221
9414431944
 </small>
</td>
</tr>
</table>
<hr style="color:#ccc">

<table width="100%" style="border:none;">
<tr style="border:none;">
<td style=" width:25%; border:none"><img src="images/stu-image.png"class="center-block img-circle img-thumbnail profile-img"></td>

<td style=" width:75%;border:none ">
<br><br>
<strong style="font-weight:bold; color:#900;">Name : </strong>'.ucfirst($employees['fname']).'&nbsp;'.ucfirst($employees['middlename']).'&nbsp;'.ucfirst($employees['lname']).'<br>
<strong style="font-weight:bold; color:#900;">Teacher ID : </strong>'.$employees['id'].'<br>
<strong style="font-weight:bold; color:#900;">Email/Login Id  : </strong>'.$employees['email'].'<br>
<strong style="font-weight:bold; color:#900;">Mobile No  : </strong>'.$employees['mobile'].'<br>
<strong style="font-weight:bold; color:#900;">Status : </strong>'.$status.'</td>

</tr>

</table>

<!------------------Personal Profile strt------------------------->

<br><br><br>
<div style="background-color:#69C; color:#fff; text-align:center; padding:4px; margin:0; font-size:22px; font-weight:bold;">Personal Profile</div>
<div>
<table style="border:1px solid #ccc; width:100%; text-align:left; margin:10px auto; padding:5px; border-collapse: collapse;">
<tr>
<th>Gender</th>
<td>'.$employees['gender'].'</td>
<th>Date Of Bearth</th>
<td>'.$employees['dob'].'</td>
</tr>
<tr>
<th>Nationality</th>
<td>'.$employees['nationality'].'</td>
<th>Maritial Status</th>
<td>'.$employees['martial_status'].'</td>
</tr>
<tr>
<th>Father/Husband Name</th>
<td>'.$employees['f_h_name'].'</td>
<th>Department</th>
<td>'.$employees['department']['name'].'</td>
</tr>
<tr>
<th>Designation</th>
<td>'.$employees['designation']['name'].'</td>
<th>Total Experience</th>
<td>'.$exp.'</td>
</tr>


</table></div>

<!------------------Personal Profile ends------------------------->


<!------------------Guardian Details strt------------------------->
<br><br><br><br><br>
<div style="background-color:#69C; color:#fff; text-align:center; padding:4px; margin:0; font-size:22px; font-weight:bold;">Guardian Info </div>
<div>
<table style="border:1px solid #ccc; width:100%; text-align:left; margin:10px auto; padding:5px; border-collapse: collapse;">
<tr>
<th>Full Name</th>
<td>'.$classessss['fullname'].'</td>
<th>Relation</th>
<td>'.$classessss['relation'].'</td>
</tr>
<tr>
<th>Qualification</th>
<td>'.$classessss['qualification'].'</td>
<th>Occupation</th>
<td>'.$classessss['occupation'].'</td>
</tr>
<tr>
<th>Total Income</th>
<td>'.$classessss['total_Income'].'</td>
<th>Mobile No	</th>
<td>'.$classessss['mobileno'].'</td>
</tr>
<tr>
<th>Email Id	</th>
<th colspan="3">'.$classessss['email'].'</th>
</tr>
<tr>
<th>Address</th>
<th colspan="3">'.$classessss['address'].'</th>
</tr>
</table></div>
<!------------------Guardian Details ends------------------------->
<br><br><br><br><br>
<table width="100%" border="0" style="border:none">
<tr border="0" style="border:none">
<td border="0" class="lg" style=" width:10%; border:none"><img src="images/L_58839.gif" alt="logo"></td>

<td border="0" style=" width:90%; font-size:17px; border:none"><strong style=" margin:6px 0; color:#5d5a50;">iDS PRIME</strong><br>
<small style="color:#5d5a50; font-weight:400;">A3 Mall Road Near Radhey Bakers,
Vidhyadhar Nagar, Jaipur 
Pincode: 302039
+91-0141-3222221
9414431944</small>
</td>
</tr>
</table>
<!------------------Address Info strt------------------------->

<br><br>
<div style="background-color:#69C; color:#fff; text-align:center; padding:4px; margin:0; font-size:22px; font-weight:bold;">Address Info </div>

<table style="border:1px solid #ccc; width:100%; text-align:left; margin:10px auto; padding:5px; border-collapse: collapse;">
<tr>
<th colspan="4">Current Address</th>
</tr>
<tr>
<th> Address</th>
<td colspan="3">'.$address['c_address'].'</td>
</tr>
<tr>
<th> City</th>
<td>'.$address['CurCity']['name'].'</td>
<th> State</th>
<td>'.$address['CurStates']['name'].'</td>
</tr>
<tr>
<th> Country</th>
<td>'.$address['CurCountry']['name'].'</td>
<th>Pincode</th>
<td>'.$address['c_pincode'].'</td>
</tr>
</table>



<table style="border:1px solid #ccc; width:100%; text-align:left; margin:10px auto; padding:5px; border-collapse: collapse;">
<tr>
<th colspan="4">Permanent Address</th>
</tr>
<tr>
<th> Address</th>
<td colspan="3">'.$address['p_address'].'</td>
</tr>
<tr>
<th> City</th>
<td>'.$address['PerCity']['name'].'</td>
<th> State</th>
<td>'.$address['PerStates']['name'].'</td>
</tr>
<tr>
<th> Country</th>
<td>'.$address['PerStates']['name'].'</td>
<th>Pincode</th>
<td>'.$address['PerStates']['p_pincode'].'</td>
</tr>



</table>
<!------------------Address Info endst------------------------->


<!------------------Documents  Info endst------------------------->
<br><br><br><br><br>
<!------------------Documents Details strt------------------------->

<div style="background-color:#69C; color:#fff; text-align:center; padding:4px; margin:0; font-size:22px; font-weight:bold;">Other Info </div>
<div>
<table style="border:1px solid #ccc; width:100%; text-align:left; margin:10px auto; padding:5px; border-collapse: collapse;">
<tr>
<th>Attendance Card ID	</th>
<td>'.$otherinfo['aadharno'].'</td>
<th>Bank Account No	</th>
<td>'.$otherinfo['accountno'].'</td>
</tr>
<tr>
<th>Reference</th>
<td>'.$otherinfo['reference'].'</td>
<th>Specialization</th>
<td>'.$otherinfo['specialization'].'</td>
</tr>
<tr>
<th>Hobbies</th>
<td>'.$otherinfo['hobbies'].'</td>
</tr>
</table></div>
<!------------------Documents Details ends------------------------->



<!------------------Student History strt
<br><br><br><br><br><br><br><br><br><br><br>
<table width="100%" border="0" style="border:none">
<tr border="0" style="border:none">
<td border="0" class="lg" style=" width:10%; border:none"><img src="images/L_58839.gif" alt="logo"></td>

<td border="0" style=" width:90%; font-size:17px; border:none"><strong style=" margin:6px 0; color:#5d5a50;">iDS PRIME</strong><br>
<small style="color:#5d5a50; font-weight:400;">A3 Mall Road Near Radhey Bakers,
Vidhyadhar Nagar, Jaipur 
Pincode: 302039
+91-0141-3222221
9414431944</small>
</td>
</tr>
</table>
<br><br>
<div style="background-color:#69C; color:#fff; text-align:center; padding:4px; margin:20px auto; margin:0; font-size:22px; font-weight:bold;">Student History</div>
<div>
<table style="border:1px solid #ccc; width:100%; text-align:left; margin:10px auto; padding:5px; border-collapse: collapse;">

<thead>
<tr>
<th>#</th>
<th>Action</th>
<th>GR No</th>
<th>Academic Year</th>
<th>Course</th>
<th>Batch</th>
<th>Section</th>
<th>Completion
Status</th>
<th>Status</th>
</tr>
</thead>

<tbody>
<tr>
<td>1</td>
<td>ENROL</td>
<td>(not set)</td>
<td>2016-17</td>
<td>Secondary</td>
<td>Year7</td>
<td>Year7-Sec1</td>
<td>in Progress</td>
<td>Active</td>
</tr>

<tr>
<td>1</td>
<td>ENROL</td>
<td>(not set)</td>
<td>2016-17</td>
<td>Secondary</td>
<td>Year7</td>
<td>Year7-Sec1</td>
<td>in Progress</td>
<td>Active</td>
</tr>


</tbody>

</table></div>
<!------------------Student History ends------------------------->

</div>

</body>
</html>';
$pdf->WriteHTML($html);
ob_end_clean();
echo $pdf->Output('Result');
exit;
?>



?>
