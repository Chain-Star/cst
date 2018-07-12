//var time = 0;
//var isSend = false;
//$(function(){
//  $('.phoneCode').click(function() {
//      var code = $("#verifyCode").val();
//      getPayPhoneVerify(code);
//  });
//})
//
///*
//*找回登录密码
//*add by zjf 2017/11/28
//*/
//function forgetPwd() {
//	 myorm = $('#forgetPwdForm').validator({
//  	rules: {
//  		checkMobile: function(element) {
//				return /^1[34578]\d{9}$/.test(element.value) || '手机号输入有误';
//			},
//			checkPwd: function(element) {
//				return /^[0-9a-zA-Z_]{6,20}$/.test(element.value) || '密码必须为6-20位数字、字母或下划线'
//			},
//			checkConfirmPwd: function(element) {
//				return /^[0-9a-zA-Z_]{6,20}$/.test(element.value) || '确认密码必须为6-20位数字、字母或下划线'
//			}
//  	},
//      fields: {
//      	'userPhone': '手机号:required;checkMobile',
//      	'verifyCode':'验证码:required;',
//			'code': '短信验证码:required;',
//          'loginPwd': '密码:required;password;length[6~20];checkPwd',
//          'password_confirm': '确认密码:required;password;match(loginPwd);length[6~20];checkConfirmPwd'
//      },
//      valid: function(form) {
//          var params = ZZHT.getParams('.ipt');
//              params['function'] = "findLoginPwd";
//          var loading = ZZHT.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
//          $.post(ZZHT.U('home/password/retrievePwd'),params,function(data,textStatus){
//              layer.close(loading);
//              var json = ZZHT.toJson(data);
//              if(json.status=='1'){
//                  ZZHT.msg("操作成功",{icon:1});
//                  setTimeout(function(){
//                      location.href=ZZHT.U('home/users/login');
//                  },1000);
//              }else{
//                  ZZHT.msg(json.msg,{icon:2});
//                  ZZHT.getVerify('#verifyImg');
//              }
//          });
//      }
//  })
//}
///*
//*找回密码发送手机短信验证码(1)
//*add by zjf 2017/11/28
//*/
//function phoneVerify() {
//	var param = new Object();
//		param['userPhone'] = $('#userPhone').val();
//  	param['code'] = $('#verifyCode').val();
//  if(param['userPhone'] == '') {
//      ZZHT.msg('请输入手机号码!', {
//          icon: 5
//      });
//      return;
//  }
//  if (param['code'] == "" || param['code'].length != 4 ) {
//      ZZHT.msg("请输入图形验证码或图形验证码长度为4", {
//          icon: 5
//      });
//      return;
//  } else {
//  	
//      var res = verifyCode.validate(document.getElementById("verifyCode").value);
//		if(res){
////			getPhoneVerifys(param);
//			alert('验证通过');
//		}else{
//			ZZHT.msg("验证码不正确", {
//	            icon: 5
//	        });
//	        return;
//		}
//  }
//}
//
///*
//*找回密码发送手机短信验证码(2)
//*add by zjf 2017/11/28
//*/
//function getPhoneVerifys(param) {
//  ZZHT.msg('正在发送短信，请稍后...', {
//      time: 600000
//  });
//  var time = 0;
//  var isSend = false;
//  param['function'] = "findLoginPwd";
//  param['type'] = 2;
//  $.post(ZZHT.U('home/password/forgetCheckCode'), param, function(data, textStatus) {
//      var json = ZZHT.toJson(data);
//      if(isSend) return;
//      isSend = true;
//      if(json.status != 1) {
//          ZZHT.msg(json.msg, {
//              icon: 5
//          });
//          ZZHT.getVerify('#verifyImg');
//          time = 0;
//          isSend = false;
//      }
//      if(json.status == 1) {
//          ZZHT.msg('短信已发送，请注册查收');
//          layer.closeAll('page');
//          time = 120;
//          $('.findPhoneCode').attr('disabled', 'disabled').css('background', '#ccc');
//          $('.findPhoneCode').html('获取手机验证码(120)');
//          $('#mobileCodeTips').css('right','-150px');
//          var task = setInterval(function() {
//              time--;
//              $('.findPhoneCode').html('获取手机验证码(' + time + ")");
//              if(time == 0) {
//                  isSend = false;
//                  clearInterval(task);
//                  $('.findPhoneCode').html("重新获取验证码");
//                  $('.findPhoneCode').removeAttr('disabled').css('background', '#ed5d2a');
//                   $('#mobileCodeTips').css('right','-130px');
//              }
//          }, 1000);
//      }
//  });
//}

/*
 *修改登录密码
 *add by zjf 2017/11/28
 */
//$(function(){
//	initeditLoginPwdValidate()
//})

//$(function() {
//
//	initeditLoginPwdValidate()
//
//	//优化代码
//
//	$('#myorm').submit(function() {
//
//		if($('#myorm').valid()) {
//
//			//通过执行的动作
//			var params = ZZHT.getParams('.ipt1');
//			var loading = ZZHT.msg('正在提交数据，请稍后...', {
//				icon: 16,
//				time: 60000
//			});
//		}
//
//		return false; //永远禁止页面表单提交
//
//	})
//
//})

function initeditLoginPwdValidate() {
	myorm = $('#myorm').validator({
		rules: {
			checkPwd: function(element) {
				return /^[0-9a-zA-Z_]{6,20}$/.test(element.value) || '密码必须为6-20位数字、字母或下划线'
			},
			checkConfirmPwd: function(element) {
				return /^[0-9a-zA-Z_]{6,20}$/.test(element.value) || '确认密码必须为6-20位数字、字母或下划线'
			}
		},
		fields: {
			'oldPwd': '原密码:required;',
			'loginPwd': '密码:required;checkPwd',
			'password_confirm': '确认密码:required;match(loginPwd);checkConfirmPwd'
		},
		valid: function(form) {
			var params = ZZHT.getParams('.ipt1');
			var loading = ZZHT.msg('正在提交数据，请稍后...', {
				icon: 16,
				time: 60000
			});
			$("#subMitButton").html('Updata Password...').attr('disabled', true);
			$.post(ZZHT.U('home/password/passEdit'), params, function(data, textStatus) {
				layer.close(loading);
				var json = ZZHT.toJson(data);
				if(json.status == '1') {
					ZZHT.msg("操作成功", {
						icon: 1,
						time: 2000
					}, function() {
						$.post(ZZHT.U('home/users/logout'), "", function(data, textStatus) {
							layer.close(loading);
							var json = ZZHT.toJson(data);
							if(json.status == '1') {
								location.href = ZZHT.U('home/users/login');
							}
						});
					});
				} else {
					ZZHT.msg(json.msg, {
						icon: 2
					}, function() {
						$("#subMitButton").html('确&nbsp;认&nbsp;修&nbsp;改').attr('disabled', false);
					});
				}
			});
		}
	})
}

/*
 *修改交易密码
 *add by zjf 2017/11/28
 */
//function editPayPwd() {
//  myorm = $('#payform').validator({
//  	rules: {
//			checkPwd: function(element) {
//				return /^\d{6}$/.test(element.value) || '密码必须为6数字'
//			},
//			checkConfirmPwd: function(element) {
//				return /^\d{6}$/.test(element.value) || '确认密码必须为6数字'
//			}
//  	},
//      fields: {
//      	'verifyCode':'验证码:required;',
//			'code': '短信验证码:required;',
//          'payPwd': '密码:required;checkPwd',
//          'password_confirm': '确认密码:required;match(payPwd);checkConfirmPwd'
//      },
//      valid: function(form) {
//          var params = ZZHT.getParams('.ipt');
//          params['function'] = "findPayPwd";
//          var loading = ZZHT.msg('正在提交数据，请稍后...', {
//              icon: 16,
//              time: 60000
//          });
//          $("#subMitButton").html('正&nbsp;在&nbsp;提&nbsp;交...').attr('disabled', true);
//          $.post(ZZHT.U('home/password/payPassEdit'), params, function(data, textStatus) {
//              layer.close(loading);
//              var json = ZZHT.toJson(data);
//              if(json.status == '1') {
//                  ZZHT.msg(json.msg, {
//                      icon: 1,
//                      time: 2000
//                  }, function() {
//                      var backUrl = $('#backUrl').val();
//                      if (backUrl !="" ) {
//                          location.href = backUrl;
//                      } else {
//                          location.href = ZZHT.U('home/account/index');
//                      }
//                  });
//              } else {
//                  ZZHT.msg(json.msg, {
//                      icon: 2
//                  },function () {
//                      $("#subMitButton").html('确&nbsp;认&nbsp;修&nbsp;改').attr('disabled', false);
//                  });
//              }
//          });
//      }
//  })
//}
//
//
////发送手机验证码
//function getPayPhoneVerify(code) {
//  if (code == "" || code.length != 4 ) {
//      ZZHT.msg("请输入图形验证码或图形验证码长度为4", {
//          icon: 5
//      });
//  }else {
//      var res = verifyCode.validate(document.getElementById("verifyCode").value);
//		if(res){
//			getPayPhoneVerifys(code);
//			alert('验证通过');
//		}else{
//			ZZHT.msg("验证码不正确", {
//	            icon: 5
//	        });
//	        return;
//		}
//  }
//}
//
//function getPayPhoneVerifys(code) {
//  ZZHT.msg('正在发送短信，请稍后...', {
//      time: 600000
//  });
//  var time = 0;
//  var isSend = false;
//  var params = new Object();
//  params['function'] = "findPayPwd";
//  params['code'] = code;
//  params['type'] = 2;
//  $.post(ZZHT.U('home/password/checkImgeCodePc'), params, function(data, textStatus) {
//      var json = ZZHT.toJson(data);
//      if(isSend) return;
//      isSend = true;
//      if(json.status != 1) {
//          ZZHT.msg(json.msg, {
//              icon: 5
//          });
//          ZZHT.getVerify('#verifyImg');
//          time = 0;
//          isSend = false;
//      }
//      if(json.status == 1) {
//          ZZHT.msg('短信已发送，请注册查收');
//          layer.closeAll('page');
//          time = 120;
//          $('.phoneCode').attr('disabled', 'disabled').css('background', '#ccc');
//          $('.phoneCode').html('获取手机验证码(120s)').css('width', '160x');
//          $('.phoneCode').css('padding', '0px 15px');
//          var task = setInterval(function() {
//              time--;
//              $('.phoneCode').html('获取手机验证码(' + time + "s)");
//              if(time == 0) {
//                  isSend = false;
//                  clearInterval(task);
//                  $('.phoneCode').html("重新获取验证码").css('padding', '0px 15px');
//                  $('.phoneCode').removeAttr('disabled').css('background', '#ed5d2a');
//              }
//          }, 1000);
//      }
//  });
//}