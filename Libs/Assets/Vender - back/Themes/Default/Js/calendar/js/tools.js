var tools = (function () {
  var toolsObj = {
    // 渲染弹出框
    // renderPopup: function (festival, obj, str, cls) {
    //   var title = obj.querySelector('.title')
    //   var date = obj.querySelector('.date')
    //   var lunar = obj.querySelector('.lunar')
    //
    //   var dateFormat = tools.strFormatDate(str)
    //   var lunarObj = ChineseCalendar.date2lunar(dateFormat);
    //
    //   title.innerHTML = festival.innerHTML
    //   date.innerHTML = dateFormat.getFullYear() + '年' + (dateFormat.getMonth() + 1) + '月' + dateFormat.getDate() + '日'
    //   lunar.innerHTML = lunarObj.lunarMonthChiness + lunarObj.lunarDayChiness + '  ·  ' + lunarObj.gzY + '年' + lunarObj.gzM + '月' + lunarObj.gzD + '日'
    //   //lunar.innerHTML = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
    // },
      renderPopup: function (festival, obj, str, cls) {
          var title = obj.querySelector('.title')
          var date = obj.querySelector('.date')
          var lunar = obj.querySelector('.lunar')

          var dateFormat = tools.strFormatDate(str)
          var lunarObj = ChineseCalendar.date2lunar(dateFormat);

          var mm = dateFormat.getMonth() + 1;
          var date_time = dateFormat.getFullYear() + '-' + mm + '-' + dateFormat.getDate();

          title.onclick = function () {
              this.parentNode.style.display = 'none'
          }

          var teaching_result = [];
          st.post(myEntrance + 'route=Admin/Teaching/Calendar/ajax_get_list_info', {date_time:date_time}, function(data){
              teaching_result = data.result;
          }, '', false);

          title.innerHTML = festival.innerHTML
          date.innerHTML = dateFormat.getFullYear() + '年' + (dateFormat.getMonth() + 1) + '月' + dateFormat.getDate() + '日'
          //lunar.innerHTML = lunarObj.lunarMonthChiness + lunarObj.lunarDayChiness + '  ·  ' + lunarObj.gzY + '年' + lunarObj.gzM + '月' + lunarObj.gzD + '日'

          if (teaching_result.length > 0) {
              var br = '';
              $.each(teaching_result, function(i, e){
                console.log(e);
                  br += '<p><a href="'+myEntrance+'route=Admin/Teaching/Calendar/edit_teaching&teaching_id='+e.teaching_id+'" target="_blank"><b style="color:darkorange;">'+e.parent_name+'</b>的宝宝<b style="color:greenyellow;">'+e.name+'</b> '+e.value+' '+e.times+'课时'+' '+e.teaching_start_time+'-'+e.teaching_end_time+' '+e.tel+'</a></p>';
              });
              lunar.innerHTML = br;
          } else {
              lunar.innerHTML = lunarObj.lunarMonthChiness + lunarObj.lunarDayChiness + '  ·  ' + lunarObj.gzY + '年' + lunarObj.gzM + '月' + lunarObj.gzD + '日'
          }
      },

    // 侧边栏及全年月份数据返回
    renderDay: function (year, n) {
      var year = year
      var month = n
      var firstDay = new Date(year, n, 1) // 月份列表第一天
      var _hmtl = ``

      for (var i = 0; i < 42; i++) {
        var allDay = new Date(year, month, i + 1 - firstDay.getDay());
        var allDay_str = tools.returnDateStr(allDay)
        var firstDay_str = tools.returnDateStr(firstDay)
        var first_lunarday = ChineseCalendar.date2lunar(allDay).lunarDayChiness
        var lunarJanuary_month = ChineseCalendar.date2lunar(allDay).lunarMonthChiness // 正月

        if (tools.returnDateStr(new Date()) === allDay_str) { // 当天
          if (first_lunarday == '初一') { // 区分初一那天
            if (lunarJanuary_month == '正月') { // 区分春节那天
              _hmtl += `<li data-time="${allDay_str}" class="cur-day lunar-january">${allDay.getDate()}</li>`
            } else {
              _hmtl += `<li data-time="${allDay_str}" class="cur-day lunar-first">${allDay.getDate()}</li>`
            }
          } else {
            _hmtl += `<li data-time="${allDay_str}" class="cur-day">${allDay.getDate()}</li>`
          }
        } else if (firstDay_str.substr(0, 6) === allDay_str.substr(0, 6)) { // 当前月份
          if (first_lunarday == '初一') {
            if (lunarJanuary_month == '正月') {
              _hmtl += `<li data-time="${allDay_str}" class="cur-month lunar-january">${allDay.getDate()}</li>`
            } else {
              _hmtl += `<li data-time="${allDay_str}" class="cur-month lunar-first">${allDay.getDate()}</li>`
            }
          } else {
            _hmtl += `<li data-time="${allDay_str}" class="cur-month">${allDay.getDate()}</li>`
          }
        } else {
          if (first_lunarday == '初一') {
            if (lunarJanuary_month == '正月') {
              _hmtl += `<li data-time="${allDay_str}" class="lunar-january">${allDay.getDate()}</li>`
            } else {
              _hmtl += `<li data-time="${allDay_str}" class="lunar-first">${allDay.getDate()}</li>`
            }
          } else {
            _hmtl += `<li data-time="${allDay_str}">${allDay.getDate()}</li>`
          }
        }
      }

      return _hmtl
    },

    // 月份详情
    renderDetailMonth: function (dayWrapper, recivedYear, recivedMonth) {
      document.getElementById('popup').style.display = 'none'
        //$('#popup').hide();
      var array = []
      var recivedDate = new Date()
      var _html = ``

      var date = new Date(recivedYear, recivedMonth, 1)
      var yyy = date.getFullYear();
      var mmm = date.getMonth() + 1;

      date.setDate(1)
      var week = date.getDay()
      date.setDate(1 - week)
      var month = date.getMonth()

      for (var i = 0; i < 42; i++) {
        if (month !== recivedMonth) {
          if (date.getDay() === 0 || date.getDay() === 6) {
            array.push({
              day: date.getDate(),
              lunar: ChineseCalendar.lunarTime(date),
              state: 'weekend',
              festival: ChineseCalendar.lunarFestival(date),
              term: ChineseCalendar.lunarTerm(date),
              dateStr: tools.returnDateStr(date)
            })
          } else {
            array.push({
              day: date.getDate(),
              lunar: ChineseCalendar.lunarTime(date),
              state: '',
              festival: ChineseCalendar.lunarFestival(date),
              term: ChineseCalendar.lunarTerm(date),
              dateStr: tools.returnDateStr(date)
            })
          }
        } else if(tools.curDay(date, recivedDate)) {
          if (date.getDay() === 0 || date.getDay() === 6) {
            array.push({
              day: date.getDate(),
              lunar: ChineseCalendar.lunarTime(date),
              state: 'weekend cur-day',
              festival: ChineseCalendar.lunarFestival(date),
              term: ChineseCalendar.lunarTerm(date),
              dateStr: tools.returnDateStr(date)
            })
          } else {
            array.push({
              day: date.getDate(),
              lunar: ChineseCalendar.lunarTime(date),
              state: 'cur-day',
              festival: ChineseCalendar.lunarFestival(date),
              term: ChineseCalendar.lunarTerm(date),
              dateStr: tools.returnDateStr(date)
            })
          }
        } else {
          if (date.getDay() === 0 || date.getDay() === 6) {
            array.push({
              day: date.getDate(),
              lunar: ChineseCalendar.lunarTime(date),
              state: 'weekend cur-month',
              festival: ChineseCalendar.lunarFestival(date),
              term: ChineseCalendar.lunarTerm(date),
              dateStr: tools.returnDateStr(date)
            })
          } else {
            array.push({
              day: date.getDate(),
              lunar: ChineseCalendar.lunarTime(date),
              state: 'cur-month',
              festival: ChineseCalendar.lunarFestival(date),
              term: ChineseCalendar.lunarTerm(date),
              dateStr: tools.returnDateStr(date)
            })
          }
        }

        date.setDate(date.getDate() + 1)
        month = date.getMonth()
      }

      var teaching_result;
      st.post(myEntrance + 'route=Admin/Teaching/Calendar/ajax_get_list', {year_month:yyy + '-' + mmm}, function(data){
          teaching_result = data.result;
      }, '', false);
        console.log(teaching_result);
        for (var j = 0; j < array.length; j++) {
            //var festival_state = array[j].festival ? 'festival show' : 'festival'
            var festival_state = 'festival'
            //var term_state = array[j].term ? 'term show' : 'term'
            var term_state = 'term'
            var teaching_count = 0;
            if (!!teaching_result[array[j].day]) {
                var teaching_state = 'teaching_count show'
                teaching_count = teaching_result[array[j].day].length;
            } else {
                var teaching_state = 'teaching_count'
            }

            var first_lunarday = array[j].lunar == '初一' ?
                ChineseCalendar.date2lunar(date).lunarMonthChiness + array[j].lunar:
                array[j].lunar

            if (array[j].lunar == '初一') {
                if (ChineseCalendar.date2lunar(date).lunarMonthChiness == '正月') {
                    var lunar_state = 'lunar first-lunarJanuary'
                } else {
                    var lunar_state = 'lunar first-lunarday'
                }
            } else {
                var lunar_state = 'lunar'
            }

            _html += `<li data-time="${array[j].dateStr}" class="${array[j].state}">
                    <p class="info">
                      <span class="${lunar_state}">${first_lunarday}</span>
                      <span class="date"><em>${array[j].day}</em>日</span>
                    </p>
                    <p class="${festival_state}">${array[j].festival}</p>
                    <p class="${term_state}">${array[j].term}</p>
                    <p class="${teaching_state}">课时（${teaching_count}）</p>
                  </li>`
        }

        dayWrapper.innerHTML = _html
    },

    nowDate: function () {
      return new Date()
    },

    returnDateStr: function (date) {
      var year = date.getFullYear();
      var month = date.getMonth();
      var day = date.getDate();

      month = month <= 9 ? ('0' + month) : ('' + month);
      day = day <= 9 ? ('0' + day) : ('' + day);

      return year + month + day;
    },

    // 是否是当天
    curDay: function (oldTime, nowTime) {
      return oldTime.getFullYear() === nowTime.getFullYear() &&
             oldTime.getMonth() === nowTime.getMonth() &&
             oldTime.getDate() === nowTime.getDate()
    },

    // 格式化日期 (如果这里传入的是字符串，那么得到的值不会按照下标去算)
    strFormatDate: function (str) {
      var date = new Date(parseInt(str.substr(0, 4)), parseInt(str.substr(4, 2)), parseInt(str.substr(6)))

      return date
    }
  }

  return toolsObj
}())