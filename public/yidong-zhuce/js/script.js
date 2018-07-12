function ck_email()  
{  
   var reg= /^\w+@\w+\.(com|cn|gov)$/;  
   var str=document.getElementById("email").value; 
   if (reg.test(str)==true){  
       document.getElementById("span-zhengque1").innerHTML="";  
       return true;  
    }else {  
        // document.getElementById("span-zhengque1").innerHTML="<font>请输入正确邮箱地址</font>";  
        return false;  
    }  
}
function mima_password()  
{  
   var  reg =/(?=.*[0-9])(?=.*[a-z])(?=.*[!@#$%^&*])(?=.*[A-Z]).{6,16}/; 
   var str=document.getElementById("mima").value;
   if (reg.test(str)==true){  
       document.getElementById("span-zhengque2").innerHTML="";  
       return true;  
    }else {  
        // document.getElementById("span-zhengque2").innerHTML="<font>密码错误</font>";  
        return false;  
    }  
}
function mima_two_password()  
{  
   var reg =/(?=.*[0-9])(?=.*[a-z])(?=.*[!@#$%^&*])(?=.*[A-Z]).{6,16}/;
   var str=document.getElementById("mima").value;  
   var str1=document.getElementById("mima-two").value;
   if (str==str1){  
       document.getElementById("span-zhengque3").innerHTML="";  
       return true;  
  }else {  
        // document.getElementById("span-zhengque3").innerHTML="<font>两次密码不相同</font>";  
        return false;  
    }  
} 
function qianbao()  
{    	
   var  reg = /^[A-Za-z0-9]{7,50}$/; 
   var str=document.getElementById("qianbao-").value;
   if (reg.test(str)==true){  
       document.getElementById("span-zhengque4").innerHTML="";  
       return true;  
    }else {  
        // document.getElementById("span-zhengque4").innerHTML="<font>钱包地址错误</font>";  
        return false;  
    }  
}
//点击调用此函数（邀请码）
function yaoqing()
{
	var reg = /^[A-Za-z0-9]+$/;
	var yaoqing_text=$("#yaoqingma").val();
	
	if(yaoqing_text.length==8&&reg.test(yaoqing_text)){
		 document.getElementById("span-zhengque5").innerHTML="";
	}else{
		
		// document.getElementById("span-zhengque5").innerHTML="<font>格式错误</font>";  
		return false; 
	}
}
