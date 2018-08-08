   
   function slide(y){
       var x = jq(y).parent();
       x = x[0];
       var child = x.children;
       if(child[1].style.display == 'none'){
        var img = jq(child[0]).children('img');
        var src = jq(img).attr("src");
        src = src.replace('drop','up');
        jq(img).attr("src",src);
        jq(child[1]).show(400);
       }
       else{
        var img = jq(child[0]).children('img');
        var src = jq(img).attr("src");
        src = src.replace('up','drop');
        jq(img).attr("src",src);
        jq(child[1]).hide(400);
       }

    }

    window.onbeforeprint = function() {
       
        var firmy = jq(".firma");
        for(var i =0;i<firmy.length;i++){
            var child = firmy[i].children;
            if(child[1].style.display == "none"){
                firmy[i].hide();
            }

        }
    };

    window.onafterprint = function() {
       
        var firmy = jq(".firma");
        for(var i =0;i<firmy.length;i++){
                firmy[i].show();
        }
    };
    function setStatus(val1,val2){
        var x = (val1 - val2);
        return x;
    }

    jq(document).ready(function(){

        var km = $(".km");
        for(var i =0;i<km.length;i++){

            var res = km[i].text().split("/");
            var km_licz = setStatus(res[0],res[1]);
            var span = "";
            if(km_licz< 0){
                span = "<span style='color:green;'>+"+Math.abs(km_licz)+"</span>";
            }
            if(km_licz > 0){
                span = "<span style='color:green;'>-"+Math.abs(km_licz)+"</span>";
            }
            if(km_licz == 0 ){
                span = "<span>+"+Math.abs(km_licz)+"</span>";
            }
            km[i].text(span);
        }
        var amount = $(".amount");
        for(var i =0;i<amount.length;i++){
            var res = amount[i].text().split("/");
            var amount_licz = setStatus(res[0],res[1]);
            var span = "";
            if(amount_licz < 0){
                span = "<span style='color:green;'>+"+Math.abs(amount_licz)+"</span>";
            }
            if(amount_licz > 0){
                span = "<span style='color:red;'>-"+Math.abs(amount_licz)+"</span>";
            }
            if(amount_licz == 0 ){
                span = "<span>+"+Math.abs(amount_licz)+"</span>";
            }
            amount[i].text(span);
        }
    });
