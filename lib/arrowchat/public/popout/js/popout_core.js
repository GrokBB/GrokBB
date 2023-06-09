(function (a) {
    function oa(r, ea) {
        r = typeof r === "string" ? document.getElementById(r) : r;
        var O = r.cloneNode(false);
        O.innerHTML = ea;
        r.parentNode.replaceChild(O, r);
        return O
    }
    a.arrowchat = function () {
		var $body = a('body');
		var $tooltip = null;
		var $tooltip_content;
		
		function hideTooltip() {
			if ($tooltip) {
				$tooltip.hide();
			}
		}
		function showTooltip($target, text, is_left, custom_left, custom_top, is_sideways) {
			if ($tooltip === null) {
				$tooltip = a('<div id="arrowchat_tooltip"><div class="arrowchat_tooltip_content"></div></div>').appendTo($body);
				$tooltip_content = a('.arrowchat_tooltip_content', $tooltip);
			}
			$tooltip_content.html(text);
			var target_offset = $target.offset();
			var target_width = $target.width();
			var target_height = $target.height();
			var tooltip_width = $tooltip.width();
			if (!custom_left) {
				custom_left = 0;
			}
			if (!custom_top) {
				custom_top = 0;
			}
			if (is_left) {
				$tooltip.css({
					top				: target_offset.top - a(window).scrollTop() - target_height - 5 - custom_top,
					left			: target_offset.left + target_width - 16 - custom_left,
					display			: "block"
				}).addClass("arrowchat_tooltip_left");
			} else if (is_sideways) {
				$tooltip.css({
					top				: target_offset.top - a(window).scrollTop() - target_height - 5 - custom_top,
					left			: target_offset.left + target_width - tooltip_width + 18 - custom_left,
					display			: "block",
					'background-position'	: tooltip_width - 128 + "px -58px"
				}).removeClass("arrowchat_tooltip_left");
			} else {
				$tooltip.css({
					top				: target_offset.top - a(window).scrollTop() - target_height - 5 - custom_top,
					left			: target_offset.left + target_width - tooltip_width + 18 - custom_left,
					display			: "block",
					'background-position'	: tooltip_width - 23 + "px -114px"
				}).removeClass("arrowchat_tooltip_left");
			}
			if (W) {
				$tooltip.css("position", "absolute");
				$tooltip.css(
					"top", 
					parseInt(a(window).height()) - parseInt($tooltip.css("bottom")) - parseInt($tooltip.height()) + a(window).scrollTop() + "px"
				);
			}
		}
        function P() {
			clearTimeout(Z);
            a.ajax({
                url: c_ac_path + "includes/json/receive/receive_buddylist.php?popout=1",
                cache: false,
                type: "post",
                dataType: "json",
                success: function (b) {
                    Va(b);
                }
            });
			if (typeof c_list_heart_beat != "undefined") {
				var BLHT = c_list_heart_beat * 1000;
			} else {
				var BLHT = 60000;
			}
            Z = setTimeout(function () {
                P()
            }, BLHT)
        }
        function Sa(b) {
         if (a(b).attr("id"))
            var c = a(b).attr("id").substr(19);
         else
            var c = "";
            if (c == "") c = a(b).parent().attr("id").substr(19);
            I(c, uc_name[c], uc_status[c], uc_avatar[c], uc_link[c])
        }
        function Va(b) {
            var c = {},
                d = "";
            c.available = "";
            c.busy = "";
            c.offline = "";
            c.away = "";
            onlineNumber = buddylistreceived = 0;
            b && a.each(b, function (i, e) {
                if (i == "buddylist") {
                    buddylistreceived = 1;
                    totalFriendsNumber = onlineNumber = 0;
                    a.each(e, function (l, f) {
                        longname = renderHTMLString(f.n).length > 16 ? renderHTMLString(f.n).substr(0, 16) + "..." : f.n;
                        if (G[f.id] != null) {
                            a("#arrowchat_popout_user_" + f.id + " .arrowchat_closebox_bottom_status").removeClass("arrowchat_available").removeClass("arrowchat_busy").removeClass("arrowchat_offline").removeClass("arrowchat_away").addClass("arrowchat_" + f.s);
                        }
                        if (f.s == "available" || f.s == "away" || f.s == "busy") onlineNumber++;
                        totalFriendsNumber++;
                        c[f.s] += '<div id="arrowchat_userlist_' + f.id + '" class="arrowchat_userlist arrowchat_buddylist_admin_' + f.admin + '" onmouseover="jqac(this).addClass(\'arrowchat_userlist_hover\');" onmouseout="jqac(this).removeClass(\'arrowchat_userlist_hover\');"><img class="arrowchat_userlist_avatar ' + d + '" src="' + f.a + '" /><span class="arrowchat_userscontentname">' + longname + '</span><span class="arrowchat_userscontentdot arrowchat_' + f.s + '"></span></div>';
                        uc_status[f.id] = f.s;
                        uc_name[f.id] = f.n;
                        uc_avatar[f.id] = f.a;
                        uc_link[f.id] = f.l
                    })
                }
                if (buddylistreceived == 1) {
                    for (buddystatus in c) if (c.hasOwnProperty(buddystatus)) c[buddystatus] == "" ? a("#arrowchat_userslist_" + buddystatus).html("") : oa("arrowchat_userslist_" + buddystatus, "<div>" + c[buddystatus] + "</div>");
                    a(".arrowchat_userlist").click(function (l) {
                        Sa(l.target)
                    });
                    R = onlineNumber;
                    totalFriendsNumber == 0 && a("#arrowchat_popout_friends").html('<div class="arrowchat_nofriends">' + lang[8] + "</div>");
                    buddylistreceived = 0
                }
				if (c_disable_avatars == 1 || u_no_avatars == 1) {
					a(".arrowchat_userlist_avatar").hide();
				}
            });
			a(".arrowchat_buddylist_admin_1").css("background-color", "#"+c_admin_bg);
			a(".arrowchat_buddylist_admin_1").css("color", "#"+c_admin_txt);
        }
        function DTitChange(name) {
            if (dtit2 != 2) {
                document.title = lang[30] + " " + name + "!";
                dtit2 = 2
            } else {
                document.title = dtit;
                dtit2 = 1
            }
            if (window_focus == false) {
                dtit3 = setTimeout(function () {
                    DTitChange(name)
                }, 1000)
            } else {
                document.title = dtit;
                clearTimeout(dtit3);
            }
        }
        function replaceURLWithHTMLLinks(text) {
            var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
            return text.replace(exp, "<a target='_blank' href='$1'>$1</a>");
        }
		RegExp.escape = function(text) {
			return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
		}
		function smileyreplace(mess) {
			for (i = 0; i < Smiley.length; i++) {
				var smiley_test = Smiley[i][1].replace(/</g, "&lt;").replace(/>/g, "&gt;");
				var check_emoticon = mess.lastIndexOf(smiley_test);
				if (check_emoticon != -1) {
					mess = mess.replace(
						new RegExp(RegExp.escape(smiley_test), 'g'),
						'<img class="arrowchat_smiley" height="16" width="16" src="' + c_ac_path + "themes/" + u_theme + '/images/smilies/' + Smiley[i][0] + '.png" alt="" />'
					);
				}
			}
			return mess;
		}
        function Oa(b) {
            I(b, uc_name[b], uc_status[b], uc_avatar[b], uc_link[b], 1)
        }
        function sa(b, c, d) {
            if (uc_name[b] == null || uc_name[b] == "") setTimeout(function () {
                sa(b, c, d)
            }, 500);
            else {
                Oa(b);
                if (d == 1) if (a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").length > 0) c = parseInt(a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").html()) + parseInt(c);
                if (c == 0) {
                    a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").remove();
                } else {
                    if (a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").length > 0) {
                        a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").html(c);
                    } else a("<div/>").addClass("arrowchat_popout_alert").html(c).appendTo(a("#arrowchat_popout_user_" + b + " .arrowchat_popout_wrap"));
                }
                y[b] = c;
                S();
            }
        }
        function S() {
            var b = "",
                c = 0;
            for (chatbox in y) if (y.hasOwnProperty(chatbox)) if (y[chatbox] != null) {
                b += chatbox + "|" + y[chatbox] + ",";
                if (y[chatbox] > 0) c = 1
            }
            Ka = c;
            b.slice(0, -1)
        }
		function M() {
			a(".arrowchat_popout_convo").css("height", a(window).height() - a("#arrowchat_popout_open_chats").height() - 50);
		}
        function H() {
            var url = c_ac_path + "includes/json/receive/receive_core.php?hash=" + u_hash_id + "&init=" + acsi;
            xOptions = a.ajax({
                url: url,
				dataType: "jsonp",
                success: function (b) {
                    V.timestamp = ma;
                    var c = "",
                        d = {};
                    d.available = "";
                    d.busy = "";
                    d.offline = "";
                    d.away = "";
                    onlineNumber = buddylistreceived = 0;
                    if (b && b != null) {
                        var i = 0;
                        a.each(b, function (e, l) {
							if (e == "popout") {
								window.close();
							}
                            if (e == "typing") {
                                a.each(l, function (f, h) {
                                    if (h.is_typing == "1") {
                                        receiveTyping(h.typing_id);
                                    } else {
                                        receiveNotTyping(h.typing_id);
                                    }
                                });
                            }
                            if (e == "messages") {
                                a.each(l, function (f, h) {
									receiveMessage(h.id, h.from, h.message, h.sent, h.self, h.old);
                                });
                                K = 1;
								D = E;
								showTimeAndTooltip();
                                d != 1 && u_sounds == 1 && !a(".arrowchat_popout_convo_input").is(":focus") && Ua();
                            }
                        });
                        j != "" && i > 0 && za(j, c)
                    }
                    if ($ != 1 && w != 1) {
                        K++;
                        if (K > 4) {
                            D *= 2;
                            K = 1
                        }
                        if (D > 12E3) D = 12E3
                    }
                    acsi++;
                    window.onblur = function () {
                        window_focus = false
                    };
                    window.onfocus = function () {
                        window_focus = true
                    };
					if (isAway == 1) {
						var CHT = c_heart_beat * 1000 * 3;
					} else {
						var CHT = c_heart_beat * 1000;
					}
					if (c_push_engine != 1) {
						CHA = setTimeout(function () {
							H()
						}, CHT);
					}
                }
            });
        }
        function X(b, c, d, i, e, l, f) {
            aa[b] != 1 && I(b, uc_name[b], uc_status[b], uc_avatar[b], uc_link[b], 1, 1);
            if (uc_name[b] == null || uc_name[b] == "") setTimeout(function () {
                X(b, c, d, i, e, l, f)
            }, 500);
            else {
                var h = "";
                if (parseInt(d) == 1) {
                    fromname = u_name;
					fromid = u_id;
                    h = " arrowchat_self";
					avatar = u_avatar;
					tooltip = lang[90];
                } else {
					fromname = uc_name[b];
					fromid = b;
					avatar = uc_avatar[b];
					tooltip = fromname;
				}
				var full_name = fromid;
                if (parseInt(l) == 1) c = c.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br>").replace(/\"/g, "&quot;");
                c = replaceURLWithHTMLLinks(c);
                c = smileyreplace(c);
                d != 1 && u_sounds == 1 && !a(".arrowchat_popout_convo_input").is(":focus") && Ua();
                separator = ":&nbsp;&nbsp;";
                if (a("#arrowchat_message_" + e).length) {
					a("#arrowchat_message_" + e + " .arrowchat_chatboxmessagecontent").html(c);
                } else {
					if (c_show_full_name != 1) {
						if (fromname.indexOf(" ") != -1) fromname = fromname.slice(0, fromname.indexOf(" "));
					}
					if (uc_status[b] == "offline" && d != 1) {
						displayMessage("arrowchat_chatroom_message_flyout", lang[13], "error");
					}

					a("#arrowchat_popout_user_" + b + "_convo .arrowchat_popout_convo").append('<div class="arrowchat_chatboxmessage arrowchat_clearfix' + h + '" id="arrowchat_message_' + e + '">' + ha(new Date(f * 1E3)) + '<div class="arrowchat_chatboxmessagefrom"><div class="arrowchat_disable_avatars_name">' + fromname + '</div><img alt="' + tooltip + '" class="arrowchat_chatbox_avatar" src="' + avatar + '" /></div><div class="arrowchat_chatboxmessage_wrapper"><div class="arrowchat_chatboxmessagecontent">' + c + "</div></div></div>");
					last_sent[b] = f;
					last_name[b] = full_name;

                    a("#arrowchat_popout_text_" + b).scrollTop(5E4);
					showTimeAndTooltip();
                }
				if (c_disable_avatars == 1 || u_no_avatars == 1) {
					a(".arrowchat_chatbox_avatar").hide();
				}
                j != b && i != 1 && sa(b, 1, 1)
            }
        }
		function showTimeAndTooltip() {
			a(".arrowchat_chatbox_avatar").mouseenter(function () {
				showTooltip(a(this), a(this).attr("alt"), false, 55, -38, 1);
			});
			a(".arrowchat_chatbox_avatar").mouseleave(function () {
				hideTooltip();
			});						
			a(".arrowchat_chatboxmessage").mouseenter(function () {
				a(this).children(".arrowchat_ts").show();
			});
			a(".arrowchat_chatboxmessage").mouseleave(function () {
				a(this).children(".arrowchat_ts").hide();
			});
			
			a(".arrowchat_lightbox").click(function (){
				a.slimbox(a(this).attr('data-id'), "", {resizeDuration:1, overlayFadeDuration:1, imageFadeDuration:1, captionAnimationDuration:1});
			});
		}
        function za(b, c) {
            if (uc_name[b] == null || uc_name[b] == "") setTimeout(function () {
                za(b, c)
            }, 500);
            else {
                a("#arrowchat_popout_text_" + b).append("<div>" + c + "</div>");
                a("#arrowchat_popout_text_" + b).scrollTop(5E4);
                G[b] = 1
            }
        }
        function ya(b) {
            if (uc_name[b] == null || uc_name[b] == "") setTimeout(function () {
                ya(b)
            }, 500);
            else j != b && a("#arrowchat_popout_user_" + b).click()
        }
        function Ua() {
            swfobject.embedSWF(c_ac_path + "themes/" + u_theme + "/sounds/new%5Fmessage.player.swf?soundswf="+c_ac_path+"themes/" + u_theme + "/sounds/new%5Fmessage.swf&autoplay=1&loops=0", "arrowchat_sound_player_holder", "1", "1", "9.0.0");
        }
        function I(b, c, d, e, l, f, h) {
            if (!(b == null || b == "")) if (uc_name[b] == null || uc_name[b] == "") if (aa[b] != 1) {
                aa[b] = 1;
                a.ajax({
                    url: c_ac_path + "includes/json/receive/receive_user.php",
                    data: {
                        userid: b
                    },
                    type: "post",
                    cache: false,
                    success: function (o) {
                        if (o) {
                            c = uc_name[b] = o.n;
                            d = uc_status[b] = o.s;
                            e = uc_avatar[b] = o.a;
                            l = uc_link[b] = o.l;
                            if (G[b] != null) {
                                a("#arrowchat_popout_user_" + b + " .arrowchat_closebox_bottom_status").removeClass("arrowchat_available").removeClass("arrowchat_busy").removeClass("arrowchat_offline").addClass("arrowchat_" + d);
                            }
                            aa[b] = 0;
                            if (c != null) {
                                qa(b, c, d, e, l, f, h)
                            } else {
                                a.post(c_ac_path + "includes/json/send/send_settings.php", {
                                    userid: u_id,
                                    unfocus_chat: b
                                }, function () {})
                            }
                        }
                    }
                })
            } else setTimeout(function () {
                I(b, uc_name[b], uc_status[b], uc_avatar[b], uc_link[b], f, h)
            }, 500);
            else qa(b, uc_name[b], uc_status[b], uc_avatar[b], uc_link[b], f, h)
        }
        function ha(b) {
            var c = "am",
                d = b.getHours(),
                i = b.getMinutes(),
                e = b.getDate();
            b = b.getMonth();
            var g = d;
            if (d > 11) c = "pm";
            if (d > 12) d -= 12;
            if (d == 0) d = 12;
            if (d < 10) d = d;
            if (i < 10) i = "0" + i;
            var l = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                f = "th";
            if (e == 1 || e == 21 || e == 31) f = "st";
            else if (e == 2 || e == 22) f = "nd";
            else if (e == 3 || e == 23) f = "rd";
			if (c_us_time != 1) {
				return e != Na ? '<span class="arrowchat_ts">' + g + ":" + i + " " + e + f + " " + l[b] + "</span>" : '<span class="arrowchat_ts">' + g + ":" + i + "</span>"
			} else {
				return e != Na ? '<span class="arrowchat_ts">' + d + ":" + i + c + " " + e + f + " " + l[b] + "</span>" : '<span class="arrowchat_ts">' + d + ":" + i + c + "</span>"
			}
        }
        function La(b) {
            a.ajax({
                cache: false,
                url: c_ac_path + "includes/json/receive/receive_history.php",
                data: {
                    chatbox: b
                },
                type: "post",
                success: function (c) {
                    if (c) {
                        a("#arrowchat_popout_user_" + b + "_convo .arrowchat_popout_convo").html("");
                        last_sent[b] = null;
                        var d = "",
                            i = uc_name[b];
                        a.each(c, function (e, l) {
                            e == "messages" && a.each(l, function (f, h) {
                                f = "";
                                if (h.self == 1) {
                                    fromname = u_name;
									fromid = u_id;
                                    f = " arrowchat_self";
                                    _aa5 = _aa4 = "";
									avatar = u_avatar;
									tooltip = lang[90];
                                } else {
                                    fromname = i;
									fromid = b;
                                    _aa4 = '<a target="_blank" href="' + uc_link[b] + '">';
                                    _aa5 = "</a>"
									avatar = uc_avatar[h.from];
									tooltip = fromname;
                                }
								var full_name = fromid;
                                var o = new Date(h.sent * 1E3);
								if (c_show_full_name != 1) {
									if (fromname.indexOf(" ") != -1) fromname = fromname.slice(0, fromname.indexOf(" "));
								}

								d += '<div class="arrowchat_chatboxmessage arrowchat_clearfix' + f + '" id="arrowchat_message_' + h.id + '">' + ha(o) + '<div class="arrowchat_chatboxmessagefrom"><div class="arrowchat_disable_avatars_name">' + fromname + '</div><img alt="' + tooltip + '" class="arrowchat_chatbox_avatar" src="' + avatar + '" /></div><div class="arrowchat_chatboxmessage_wrapper"><div class="arrowchat_chatboxmessagecontent">' + h.message + "</div></div></div>";
								last_sent[h.from] = h.sent;
								last_name[h.from] = full_name;
                            })
                        });
                        if (a("#arrowchat_popout_text_" + b).length > 0) oa("arrowchat_popout_text_" + b, "<div>" + d + "</div>" + document.getElementById("arrowchat_popout_text_" + b).innerHTML);
                        else document.getElementById("arrowchat_popout_text_" + b).innerHTML = "<div>" + d + "</div>";
                        a("#arrowchat_popout_text_" + b).scrollTop(5E4);
						showTimeAndTooltip();
						a(".arrowchat_image_message img").one("load", function() {
						  a("#arrowchat_popout_text_" + b).scrollTop(5E4);
						}).each(function() {
						  if(this.complete) a(this).load();
						});
						if (c_disable_avatars == 1 || u_no_avatars == 1) {
							a(".arrowchat_chatbox_avatar").hide();
						}
                    }
                }
            })
        }
        function ea(atid) {
            a.post(c_ac_path + "includes/json/send/send_typing.php", {
                userid: u_id,
                typing: atid,
                untype: 1
            }, function () {});
            fa = -1
        }
        function O(b, c, d) {
            clearTimeout(pa);
            pa = setTimeout(function () {
                ea(d)
            }, 5E3);
            if (fa != d) {
                a.post(c_ac_path + "includes/json/send/send_typing.php", {
                    userid: u_id,
                    typing: d
                }, function () {});
                fa = d
            }
            if (b.keyCode == 13 && b.shiftKey == 0) {
                var i = a(c).val();
                i = i.replace(/^\s+|\s+$/g, "");
                a(c).val("");
                a(c).focus();
				if (c_send_priv_msg == 1 && i != "") {
					displayMessage("arrowchat_chatroom_message_flyout", lang[209], "error");
				} else {
					i != "" && a.post(c_ac_path + "includes/json/send/send_message.php", {
						userid: u_id,
						to: d,
						message: i
					}, function (e) {
						if (e) {
							X(d, i, "1", "1", e, 1, Math.floor((new Date).getTime() / 1E3));
							a("#arrowchat_popout_user_" + d + "_convo .arrowchat_popout_convo").scrollTop(a("#arrowchat_popout_user_" + d + "_convo .arrowchat_popout_convo")[0].scrollHeight)
						}
						K = 1;
						if (D > E) {
							D = E;
							clearTimeout(Y);
							Y = setTimeout(function () {
								H()
							}, E)
						}
					});
				}
                return false
            }
        }
        function Ca(b, c, d) {
            a("#arrowchat_popout_user_" + d + "_popup .arrowchat_popout_convo").scrollTop(a("#arrowchat_popout_user_" + d + "_convo .arrowchat_popout_convo")[0].scrollHeight)
        }
        function qa(b, c, d, e, l, f) {
            if (G[b] != null || a("#arrowchat_popout_user_"+b+"_convo").length > 0) {
                if (!a("#arrowchat_user_" + b).hasClass("arrowchat_tabclick") && f != 1) {
					j = b;
					a(".arrowchat_popout_convo_wrapper").removeClass("arrowchat_popout_convo_focused");
					a(".arrowchat_popout_tab").removeClass("arrowchat_popout_focused");
					a("#arrowchat_popout_user_" + b + "_convo").addClass("arrowchat_popout_convo_focused");
					a("#arrowchat_popout_user_" + b).addClass("arrowchat_popout_focused");
					if (ca[b] != 1) {
						La(b);
						ca[b] = 1
					}
                }
            } else {
                shortname = renderHTMLString(c).length > 12 ? renderHTMLString(c).substr(0, 12) + "..." : c;
                longname = renderHTMLString(c).length > 24 ? renderHTMLString(c).substr(0, 24) + "..." : c;
                a("<div/>").attr("id", "arrowchat_popout_user_" + b).addClass("arrowchat_popout_tab").html('<div class="arrowchat_bar_left"><div class="arrowchat_popout_wrap"><div class="arrowchat_popout_tab_name">' + shortname + '</div><div class="arrowchat_closebox_bottom_status arrowchat_' + d + '" style="margin-top:4px;"></div></div></div><div class="arrowchat_popout_right"><div class="arrowchat_closebox_bottom"></div></div>').appendTo(a("#arrowchat_popout_container"));
				a("<div/>").attr("id", "arrowchat_popout_user_" + b + "_convo").addClass("arrowchat_popout_convo_wrapper").html('<div id="arrowchat_popout_text_' + b + '" class="arrowchat_popout_convo"></div><div class="arrowchat_popout_input_container"><div class="arrowchat_smiley_button"><div class="arrowchat_more_wrapper arrowchat_smiley_popout"><div class="arrowchat_more_popout"><div class="arrowchat_smiley_box"></div><i class="arrowchat_more_tip"></i></div></div></div><div id="arrowchat_upload_button_' + b + '"><div id="arrowchat_chatroom_uploader"> </div></div><div class="arrowchat_popout_input_wrapper"><textarea class="arrowchat_popout_convo_input"></textarea></div></div>').appendTo(a("#arrowchat_popout_chat"));
                a("#arrowchat_popout_user_" + b + " .arrowchat_closebox_bottom").mouseenter(function () {
                    a(this).addClass("arrowchat_closebox_bottomhover")
                });
                a("#arrowchat_popout_user_" + b + " .arrowchat_closebox_bottom").mouseleave(function () {
                    a(this).removeClass("arrowchat_closebox_bottomhover")
                });
                a("#arrowchat_popout_user_" + b + " .arrowchat_closebox_bottom").click(function () {
                    a.post(c_ac_path + "includes/json/send/send_settings.php", {
                        userid: u_id,
                        close_chat: b,
                        tab_alert: 1
                    }, function () {});
					a("#arrowchat_popout_user_" + b + "_convo").remove();
                    a("#arrowchat_popout_user_" + b).remove();
                    if (j == b) j = "";
                    y[b] = null;
                    G[b] = null;
                    ca[b] = 0;
                });
                a("#arrowchat_popout_user_" + b + "_convo .arrowchat_popout_convo_input").keydown(function (h) {
                    return O(h, this, b)
                });
                a("#arrowchat_popout_user_" + b + "_convo .arrowchat_popout_convo_input").keyup(function (h) {
                    return Ca(h, this, b)
                });
				a("#arrowchat_popout_user_" + b).click(function () {
					var tba = 0;
                    if (a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").length > 0) {
                        tba = 1;
                        a("#arrowchat_popout_user_" + b + " .arrowchat_popout_alert").remove();
                        G[b] = 0;
                        y[b] = 0;
                        S()
                    }
                    if (a(this).hasClass("arrowchat_popout_focused")) {
                        a(this).removeClass("arrowchat_popout_focused");
						a("#arrowchat_popout_user_" + b + "_convo").removeClass("arrowchat_popout_convo_focused");
                        j = "";
                        a.post(c_ac_path + "includes/json/send/send_settings.php", {
                            userid: u_id,
                            unfocus_chat: b,
                            tab_alert: 1
                        }, function () {})
                    } else {
                        if (j != "") {
                            a("#arrowchat_popout_user_" + j).removeClass("arrowchat_popout_focused");
							a("#arrowchat_popout_user_" + j + "_convo").removeClass("arrowchat_popout_convo_focused");
                            j = ""
                        }
                        if (ca[b] != 1) {
                            La(b);
                            ca[b] = 1
                        }
                        a.post(c_ac_path + "includes/json/send/send_settings.php", {
                            userid: u_id,
                            focus_chat: b,
                            tab_alert: tba
                        }, function () {});
						a(this).addClass("arrowchat_popout_focused");
						a("#arrowchat_popout_user_" + b + "_convo").addClass("arrowchat_popout_convo_focused");
						j = b;
						a("#arrowchat_popout_user_" + b + "_convo .arrowchat_popout_convo_input").focus()
					}
                    a("#arrowchat_popout_user_" + b + "_convo .arrowchat_popout_convo").scrollTop(a("#arrowchat_popout_user_" + b + "_convo .arrowchat_popout_convo")[0].scrollHeight);
				});
				var smiley_exist = [];
				for (var i = 0; i < Smiley.length; i++) {
					if (a.inArray(Smiley[i][0], smiley_exist) > -1) {
					} else {
						a("#arrowchat_popout_user_" + b + "_convo .arrowchat_smiley_box").append('<div class="arrowchat_smiley_wrapper" data-id="'+i+'"><img class="arrowchat_smiley" src="'+c_ac_path+'themes/'+u_theme+'/images/smilies/'+Smiley[i][0]+'.png" alt="" /></div>');
						smiley_exist.push(Smiley[i][0]);
					}
				}
				a("#arrowchat_popout_user_" + b + "_convo .arrowchat_smiley_button").mouseenter(function () {
					a(this).addClass("arrowchat_smiley_button_hover")
				});
				a("#arrowchat_popout_user_" + b + "_convo .arrowchat_smiley_button").mouseleave(function () {
					a(this).removeClass("arrowchat_smiley_button_hover");
				});
				a("#arrowchat_popout_user_" + b + "_convo .arrowchat_smiley_wrapper").click(function () {
					var smiley_code = a(this).attr("data-id");
					var existing_text = a("#arrowchat_popout_user_" + b + "_convo .arrowchat_popout_convo_input").val();
					a("#arrowchat_popout_user_" + b + "_convo .arrowchat_popout_convo_input").focus().val('').val(existing_text + Smiley[smiley_code][1]);
				});
				a("#arrowchat_popout_user_" + b + "_convo .arrowchat_smiley_button").click(function () {
					if (a(".arrowchat_smiley_popout").is(":visible")) {
						a(".arrowchat_smiley_popout").children(".arrowchat_more_popout").hide();
					} else {
						a(".arrowchat_smiley_popout").children(".arrowchat_more_popout").show();
					}
				});
				if (c_disable_smilies == 1) {a(".arrowchat_smiley_button").hide();}
				uploadProcessing(b);
				if (c_file_transfer != 1) {
					a("#arrowchat_upload_button_" + b).hide();
				}
				f != 1 && a("#arrowchat_popout_user_" + b).click();
				y[b] = 0;
				G[b] = 0;
            }
			M();
        }
		function uploadProcessing(b) {
			var ts67 = Math.round(new Date().getTime());
			var path = c_ac_path.replace("../", "/");
			a("#arrowchat_upload_button_" + b).uploadify({
				'swf': path + 'includes/js/uploadify/uploadify.swf',
				'uploader': path + 'includes/classes/class_uploads.php',
				'hideButton': true,
				'buttonText': ' ',
				'wmode': 'transparent',
				'formData': {
					'unixtime': ts67,
					'user': u_id
				},
				'height': 25,
				'width': 24,
				'multi': false,
				'auto': true,
				'fileTypeExts': '*.jpg;*.gif;*.png;*.zip;*.rar;*.jpeg;*.txt;*.doc;*.mp3;*.wmv;*.avi;*.mp4;*.docx;*.wav',
				'fileTypeDesc': 'Supported File Types (.JPG, .JPEG, .GIF, .PNG, .WMV, .AVI, .MP4, .ZIP, .RAR, .MP3, .WAV, .TXT, .DOC, .DOCX)',
				'fileSizeLimit' : c_max_upload_size + 'MB',
				'onSelect': function () {
				
				},
				'onCancel': function () {

				},
				'onUploadComplete': function () {
					uploadProcessing(b);
				},
				'onUploadError': function (file, errorCode, errorMsg, errorString) {
					displayMessage("arrowchat_chatroom_message_flyout", lang[151], "error");
				},
				'onUploadSuccess': function (file) {
					var uploadType = "file",
						fileType = file.type.toLowerCase();
					if (fileType == ".png" || fileType == ".gif" || fileType == ".jpg" || fileType == ".jpeg")
						uploadType = "image";
						
					a.post(c_ac_path + "includes/json/send/send_message.php", {
						userid: u_id,
						to: b,
						message: uploadType + "{" + ts67 + "}{" + file.name + "}"
					}, function (e) {
						if (e == "-1") {
							displayMessage("arrowchat_chatroom_message_flyout", lang[102], "error");
						} else {
							displayMessage("arrowchat_chatroom_message_flyout", lang[68], "notice");
						}
						a(".arrowchat_popout_convo").scrollTop(5E4);
					});
				}
			});
			a("#arrowchat_upload_button_" + b).mouseenter(function () {
				a(this).addClass("arrowchat_upload_button_hover")
			});
			a("#arrowchat_upload_button_" + b).mouseleave(function () {
				a(this).removeClass("arrowchat_upload_button_hover");
			});
		}
		function displayMessage(id, message, type) {
			clearTimeout(message_timeout);
			if (a("#" + id).is(":visible")) {
				a("#" + id).hide(function() {
					a("#" + id + " .arrowchat_message_text").html(message);
					type == "error" && a(".arrowchat_message_box").css("background-color", "#ffe2e2").css("border-bottom", "1px solid #ffbebe");
					type == "notice" && a(".arrowchat_message_box").css("background-color", "#fffae2").css("border-bottom", "1px solid #ffecbe");
					a("#" + id).show();
				});
			} else {
				type == "error" && a(".arrowchat_message_box").css("background-color", "#ffe2e2").css("border-bottom", "1px solid #ffbebe");
				type == "notice" && a(".arrowchat_message_box").css("background-color", "#fffae2").css("border-bottom", "1px solid #ffecbe");
				a("#" + id + " .arrowchat_message_text").html(message);
				a("#" + id).show();
			}
			message_timeout = setTimeout(function () {
				a("#" + id).hide();
			}, 5000);
		}
		function stripslashes(str) {
			str=str.replace(/\\'/g,'\'');
			str=str.replace(/\\"/g,'"');
			str=str.replace(/\\0/g,'\0');
			str=str.replace(/\\\\/g,'\\');
			return str;
		}
		function receiveMessage(id, from, message, sent, self, old) {
			var c = "",
			ma = id;
			clearTimeout(dtit3);
			DTitChange(uc_name[from]);
			if (j == from && uc_name[from] != "" && uc_name[from] != null) {
				var container = a("#arrowchat_popout_text_" + from)[0].scrollHeight - a("#arrowchat_popout_text_" + from).scrollTop() - 10;
				var container2 = a("#arrowchat_popout_text_" + from).outerHeight();
				var o = uc_name[from];
				if (uc_status[from] == "offline") {
					P();
				}
				f = "";
				if (self == 1) {
					fromname = u_name;
					fromid = u_id;
					f = " arrowchat_self";
					_aa5 = _aa4 = "";
					avatar = u_avatar;
					tooltip = lang[90];
				} else {
					fromname = o;
					fromid = from;
					_aa4 = '<a target="_blank" href="' + uc_link[from] + '">';
					_aa5 = "</a>";
					avatar = uc_avatar[from];
					tooltip = fromname;
				}
				var full_name = fromid;
				message = stripslashes(message);
				message = replaceURLWithHTMLLinks(message);
				if (a("#arrowchat_message_" + id).length) {
					a("#arrowchat_message_" + id + " .arrowchat_chatboxmessagecontent").html(message);
				} else {
					if (c_show_full_name != 1) {
						if (fromname.indexOf(" ") != -1) fromname = fromname.slice(0, fromname.indexOf(" "));
					}

					a("#arrowchat_popout_user_" + from + "_convo .arrowchat_popout_convo").append('<div class="arrowchat_chatboxmessage arrowchat_clearfix' + f + '" id="arrowchat_message_' + id + '">' + ha(new Date(sent * 1E3)) + '<div class="arrowchat_chatboxmessagefrom"><div class="arrowchat_disable_avatars_name">' + fromname + '</div><img alt="' + tooltip + '" class="arrowchat_chatbox_avatar" src="' + avatar + '" /></div><div class="arrowchat_chatboxmessage_wrapper"><div class="arrowchat_chatboxmessagecontent">' + message + "</div></div></div>");
					last_sent[from] = sent;
					last_name[from] = full_name;
				}
				if (container <= container2 || !a("#arrowchat_popout_user_"+from).hasClass("arrowchat_popout_focused")) {
					a("#arrowchat_popout_text_" + from).scrollTop(5E4);
					a(".arrowchat_image_message img").one("load", function() {
						setTimeout(function () {
							a("#arrowchat_popout_text_" + from).scrollTop(5E4);
						}, 500);
					}).each(function() {
					  if(this.complete) a(this).load();
					});
				} else {
					displayMessage("arrowchat_chatroom_message_flyout", lang[134], "notice");
				}
				if (c_disable_avatars == 1 || u_no_avatars == 1) {
					a(".arrowchat_chatbox_avatar").hide();
				}
			} else {
				f = 0;
				if (!(a("#arrowchat_popout_user_" + from).length > 0)) {
					a.post(c_ac_path + "includes/json/send/send_settings.php", {
						unfocus_chat: from
					}, function () {});
					f = 1
				}
				X(from, message, self, old, id, 0, sent);
				j == "" && 0 && ya(from)
			}
			j != "" && za(j, c);
		}
		function receiveTyping(id) {
			a("#arrowchat_popout_user_"+id+" .arrowchat_closebox_bottom_status").addClass("arrowchat_typing")
		}
		function receiveNotTyping(id) {
			a("#arrowchat_popout_user_"+id+" .arrowchat_closebox_bottom_status").removeClass("arrowchat_typing")
		}
		function pushSubscribe() {
			if (c_push_engine == 1) {
				push.subscribe({ "channel" : "u"+u_id, "callback" : function(data) { pushReceive(data); } });
			}
		}
		function pushReceive(data) {
			if ("typing" in data) {
				receiveTyping(data.typing.id);
			}
			if ("nottyping" in data) {
				receiveNotTyping(data.nottyping.id);
			}
			if ("messages" in data) {
				receiveMessage(data.messages.id, data.messages.from, data.messages.message, data.messages.sent, data.messages.self, data.messages.old);
				u_sounds == 1 && !a(".arrowchat_popout_convo_input").is(":focus") && Ua();
				showTimeAndTooltip();
				K = 1;
				D = E;
			}
		}
		function renderHTMLString(string) {
			var render = a("<div/>").attr("id", "arrowchat_render").html(string).appendTo('body');
			var new_render = a("#arrowchat_render").html()
			render.remove();
			return new_render;
		}
        var bounce = 0,
            bounce2 = 0,
            count = 0,
            V = {},
            dtit = document.title,
            dtit2 = 1,
            dtit3, window_focus = true,
            xa = {},
            j = "",
            crou = "",
            $ = 0,
            w = 0,
            bli = 1,
			isAway = 0,
            chatroomreceived = 0,
            msgcount = 0,
            W = false,
            Y, Z, E = 3E3,
            Crref2, Ccr = -1,
            D = E,
            K = 1,
            ma = 0,
            R = 0,
            m = "",
            Ka = 0,
            crt = {},
            crt2 = {},
            y = {},
            G = {},
            aa = {},
            ca = {},
			last_sent = {},
			last_name = {},
            Aa = new Date,
            Na = Aa.getDate(),
            ab = Math.floor(Aa.getTime() / 1E3),
            acsi = 1,
            Q = 0,
			message_timeout,
            fa = -1,
            acp = "Powered By <a href='http://www.arrowchat.com/' target='_blank'>ArrowChat</a>",
            pa = 0,
            B, N;
        var _ts = "",
            _ts2;
        for (d = 0; d < Themes.length; d++) {
            if (Themes[d][2] == u_theme) {
                _ts2 = "selected";
            } else {
                _ts2 = "";
            }
            _ts = _ts + "<option value=\"" + Themes[d][0] + "\" " + _ts2 + ">" + Themes[d][1] + "</option>";
        }
        arguments.callee.videoWith = function (b) {
            var win = window.open(c_ac_path + 'public/video/?rid=' + b, 'audiovideochat', "status=no,toolbar=no,menubar=no,directories=no,resizable=no,location=no,scrollbars=no,width=650,height=610");
            win.focus();
        };
		function Xa() {
			if (c_push_engine == 1) {
				push = PUBNUB.init({
					publish_key   : c_push_publish,
					subscribe_key : c_push_subscribe
				});
			}
			if (c_push_engine == 1) {
				pushSubscribe();
			}
			H();
			P();
			M();
			a(window).bind("resize", M);
			for (var d = 0; d < unfocus_chat.length; d++) {
				I(unfocus_chat[d], uc_name[unfocus_chat[d]], uc_status[unfocus_chat[d]], uc_avatar[unfocus_chat[d]], uc_link[unfocus_chat[d]], "1");
			}
			if (u_chat_open != 0) { 
				I(u_chat_open, uc_name[u_chat_open], uc_status[u_chat_open], uc_avatar[u_chat_open], uc_link[u_chat_open], "0")
			}
		}
        a.ajaxSetup({
            scriptCharset: "utf-8",
            cache: false
        });
        arguments.callee.runarrowchat = function () {
            Xa()
        };
    }
})(jqac);
(jqac);
jqac(document).ready(function () {
    jqac.arrowchat();
    jqac.arrowchat.runarrowchat()
});