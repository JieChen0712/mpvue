var getParam = function(name){
        var search = document.location.search;
        var pattern = new RegExp("[?&]"+name+"\=([^&]+)", "g");
        var matcher = pattern.exec(search);
        var items = null;
        if(null != matcher){
                try{
                        items = decodeURIComponent(decodeURIComponent(matcher[1]));
                }catch(e){
                        try{
                                items = decodeURIComponent(matcher[1]);
                        }catch(e){
                                items = matcher[1];
                        }
                }
        }
        return items;
    };
    if(getParam("pd")=="tz"){
        producttz = "<img id='PImg' src='"+pub+"/images/tz.jpg' style='width:100%'><h2 style='text-align:center;margin:0px;'>植萃卉集守护套装</h2><h3 style='color:red;text-align:center;margin:5px 0px;'>￥298.00</h3><span>系列介绍：手如柔荑，指如青葱，植萃力量，守护四季。抚平十指连心的燥动，长效衡润，卉集四部曲，焕活女人的第二张脸。全系列精粹集合白羽扇豆洁净力、玫瑰滋养力、百合嫩滑力、金盏花润泽力——植萃四力成分，破译手部密码，开启天然屏障，深彻修源，水润赋活盈亮肌肤，让你美而不同！<br/><br/></span>";
        $(function(){
            $('.product').append(producttz);
        })
    }else if(getParam("pd")=="sm"){
        producttz = "<img id='PImg' src='"+pub+"/images/sm.jpg' style='width:100%'><h2 style='text-align:center;margin:0px;'>玫瑰滋养水漾手膜</h2><h3 style='color:red;text-align:center;margin:5px 0px;'>￥98.00</h3><span>主要成分：玫瑰（舒润盈肌、紧柔抗皱）、马齿苋（光泽娇肤、滋营透亮）、小白菊（舒缓修护、水活肌底）。<br/><br/>产品介绍：玫瑰精华萃取物，保湿、补水、防皱三重功效融汇指尖膜法，舒缓、滋养令手部肌肤沉浸水漾愉悦中，馥郁迷人香气触启肌肤之美，宛如玫瑰与肌肤的法式湿吻，嗅到她的气息，十指连心沁甜芳扉，静听水漾在耳畔抚平岁月的声音。<br/><br/>产品说明：富含玫瑰精华，兼具保湿、补水、防皱功效，小白菊提取物可舒缓修护肌肤，马齿苋提取物则深层滋养肌肤，并透亮肤色，增加手部肌肤光泽度。辅以积雪草、母菊、虎杖等多种植物成分，能有效舒缓保湿肌肤，让肌肤弹润嫩滑，同时紧致肌肤，使手部纤柔丝滑，饱满光泽富有弹性。配合手部磨砂及手霜使用效果更佳。<br/><br/>玫瑰花：恢复肌肤的柔软及紧致，让表皮纹理更细腻，改善肌肤粗糙现象。香气触动人的味蕾，带来舒缓，宁静而又愉悦的心情。玫瑰中富含维生素C成分，能带给肌肤年轻状态，帮助抵抗肌肤衰老情况。玫瑰中的糖具有强大的保湿和锁水功能，对于干燥的肌肤，能够起到显著的保湿和锁水效果，同时，玫瑰中含有的硒、锌、铜等微量元素更能够清除肌肤的自由基，让肌肤恢复水嫩光泽。<br/><br/></span>";
        $(function(){
            $('.product').append(producttz);
        })
    }
    else if(getParam("pd")=="ss30"){
        producttz = "<img id='PImg' src='"+pub+"/images/ss30.jpg' style='width:100%'><h2 style='text-align:center;margin:0px;'>金盏花凝光润手霜</h2><h3 style='color:red;text-align:center;margin:5px 0px;'>￥48.00</h3><span>主要成分：金盏花（紧致熠然、粹能凝光）、百合（清润嫩滑、赋活燥肌）、紫玉兰（净颜润肤、弹润水盈）。<br/><br/>产品介绍：富含金盏花精华，丰盈水润即现凝光玉手，形成保护水膜，长效呵护您的双手，随时滋养芳香，耀漫守护，时刻滑动心底一缕丝动，丝光润滑此刻尽绽。<br/><br/>产品说明：富含金盏花提取物可紧致修护，丰盈肌肤，呈现柔亮光泽，白花百合鳞茎提取物，修护因干燥引起的肌肤问题，紫玉兰花提取物可透亮、滋养肌肤，为肌肤补充营养物质。辅以植物有效成分如洋甘菊、蔷薇、扭刺仙人掌提取物，可形成手部保护水膜，远离干燥，有效滋养肌肤，令手部肌肤柔嫩，润肤凝光。<br/><br/>金盏花：金盏花果实含丰富的维生素A，可预防色素沉淀、增进皮肤光泽与弹性、减缓衰老、避免肌肤松弛生皱。可促进肌肤的清洁柔软，镇定肌肤，改善敏感性肤质。促进皮肤的新陈代谢，尤其针对干燥的肌肤，有高度的滋润效果。集合舒缓敏感、补水保湿、清爽滋养三大功效。<br/><br/></span>";
        $(function(){
            $('.product').append(producttz);
        })
    }else if(getParam("pd")=="ss80"){
        producttz = "<img id='PImg' src='"+pub+"/images/ss80.jpg' style='width:100%'><h2 style='text-align:center;margin:0px;'>百合嫩滑润手霜</h2><h3 style='color:red;text-align:center;margin:5px 0px;'>￥78.00</h3><span>主要成分：百合（清润嫩滑、赋活燥肌）、紫玉兰（靓透润肤、弹润水盈）、金盏花（紧致熠然、粹能凝光）。<br/><br/>产品介绍：清润质地绽放百合香气，一抹赋活干燥肌肤，呵护肌肤，柔滑美肌、纤纤素手，来自紫玉兰的有效养护，回溯嫩滑双手，清新浪漫、心心相印。<br/><br/>产品说明：富含白花百合鳞茎提取物，修护干燥因干燥引起的肌肤问题，紫玉兰花提取物可透亮、滋养肌肤，为肌肤补充营养物质，金盏花提取物可紧致修护。提取植物有效成分如洋甘菊、蔷薇、扭刺仙人掌提取物，可修护皮肤水膜，滋润肌肤，令手部肌肤柔嫩保湿，呈现柔润弹滑，同时防止手部干裂，让手部肌肤告别冬日各种问题，滋养修护。<br/><br/>百合：修护因干燥引起的肌肤问题。百合具有抗氧化作用，能阻断活性氧和自由基的生成，水解蛋白极易被细胞吸收，修复受损肌肤，促进新陈代谢，形成天然膜层，增加皮肤张力。保湿效果佳，亲水因子提升皮肤机能，使皮肤保持细腻柔滑，赋予光泽与弹性。<br/><br/></span>";
        $(function(){
            $('.product').append(producttz);
        })
    }else if(getParam("pd")=="msg"){
        producttz = "<img id='PImg' src='"+pub+"/images/msg.jpg' style='width:100%'><h2 style='text-align:center;margin:0px;'>白羽扇豆深焕磨砂膏</h2><h3 style='color:red;text-align:center;margin:5px 0px;'>￥108.00</h3><span>主要成分：白羽扇豆（紧致修护、隔离焕肌）、积雪草（润泽光滑、优化焕活）、杏仁（研磨盈透、臻现美颜）。<br/><br/>产品介绍：白羽扇豆籽精粹，游走于肌肤，开启优化焕肌之旅，轻柔按摩间，细微磨砂颗粒与娇肤来次原始的激情碰撞，带走角质、抚平干燥，温润不伤手的极净体验。<br/><br/>产品说明：白羽扇豆籽提取精华具有紧致修护作用，减少肌肤受到外界的伤害，杏仁提取物可补充肌肤所缺少的维生素E，舒缓柔肤，积雪草提取物可紧致表皮肌肤，使手部皮肤润泽光滑。含有极微霍霍巴酯类颗粒，质地温和不伤手，在去除手部角质同时，为肌肤提供充分的按摩，有效滋润肌肤，使用后，让肌肤弹润丝滑。配合手膜及手霜使用，效果更佳。<br/><br/>白羽扇豆：修复，补水、促进皮肤细胞生长。羽扇豆主要成分有小分子谷氨酸肽。能有效促进皮肤蛋白质的合成，防止水份流失，并加强皮肤的屏障，重建并修复受损表皮，防止水份流失，促进角质细胞分化，提供完美的保湿及再生功效，加强皮肤屏障功能三重保湿，具有良好的修复，补水及促进皮肤细胞再生的作用。<br/><br/></span>";
        $(function(){
            $('.product').append(producttz);
        })
    }else if(getParam("pd")=="sss"){
        producttz = "<img id='PImg' src='"+pub+"/images/sss.jpg' style='width:100%'><h2 style='text-align:center;margin:0px;'>牛油果纤致滋润霜（单品）</h2><h3 style='color:red;text-align:center;margin:5px 0px;'>￥238.00</h3><span>主要成分：牛油果（纤致肌肤、锁水纤柔）、桑叶（有效舒缓、激活弹力）、仙人掌（净透肌颜、臻柔水护）、黄芩（柔缓玉肌、光致肌肤）。<br/><br/>产品介绍：牛油果树果脂与肌里的放肆缠绵，纤柔致美，草本植萃精华舒缓滋养，双效修护，增加肌肤弹性，守护丝滑肌肤。<br/><br/>产品说明：富含牛油果树果脂，可紧致肌肤，黄芩根提取物可舒缓全身肌肤，带来SPA级柔肤体验，扭刺仙人掌茎精华则可补水保湿，净透滋养润肌。蕴含多种植物精萃，含有肌肤所需的多种营养物质，调理肤质并紧致肤表，达到紧致、光滑肌肤作用，让你的身材日趋窈窕迷人。<br/><br/>牛油果：牛油果富含能促进女性荷尔蒙和雌性激素分泌的维生素E，以及将脂肪分解为脂肪酸和水分的消化酵素，还含有提高代谢燃烧脂肪的油酸，具备纤形功效。牛油含有丰富的甘油酸、蛋白质和维他命，是天然的抗氧化剂，不仅能柔软和滋润肌肤，还能收缩粗大的毛孔。尤其适合干性皮肤，能有效滋润肌肤，润而不腻，天赐纤形美容恩物。<br/><br/></span>";
        $(function(){
            $('.product').append(producttz);
        })
    }