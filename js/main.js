console.log("[angular.module] start");
var app = angular.module('app', ["ngRoute", "ngCookies"]);

app.config(function($routeProvider, $locationProvider) {
    console.log("[app.config] start");

    $routeProvider
        .when("/bar/:barId", {
            templateUrl : "/pages/login.html", 
            controller: "loginCtrl"
        })
        .when("/bar/:barId/play", {
            templateUrl : "/pages/play.html",
            controller: "playCtrl"
        })
        .otherwise({
			redirectTo: '/'
		});;

    // configure html5 to get links working on jsfiddle
    $locationProvider.html5Mode(true);

});

app.controller('loginCtrl', function($scope, $http, $rootScope, $routeParams, $interval, $location, $cookies){
    console.log("[loginCtrl] start", $routeParams);
    $scope.params = $routeParams;

    $scope.myForm = {}; 

    $scope.goPlay = () => {
        console.log("[goPlay] start", $scope.myForm);
        
        if (!$scope.myForm.$valid) {
            return;
        }

        console.log("[goPlay] Save data...");
        
        $scope.code = $scope.code.toUpperCase();

        if (typeof $scope.email === 'undefined') {
            $scope.email = "";
         }

        $cookies.put("name", $scope.name);
        $cookies.put("rut", $scope.rut);
        $cookies.put("email", $scope.email);
        $cookies.put("code", $scope.code);
    
        console.log("[goPlay] Checking code...");

        $http({
            url: "api/tokens/", 
            method: "GET",
            params: {
                code: $scope.code
            }
        }).then(function(response) {
            console.log("[goPlay] success:", response);
            $data = response.data;
            if ($data.length == 1) {
                $row = $data[0];
                console.log("[goPlay] burned:", $row.burned);

                if ($row.burned == "0") {
                    console.log("[goPlay] codigo disponible");
                    $location.url("/bar/" + $scope.params.barId + "/play");
                    return true;
                } else {
                    console.log("[goPlay] codigo quemado");

                    $scope.text1 = "C\u00F3digo usado";
                    $scope.text2 = "Intenta con otro c\u00F3digo Cristal";

                    $scope.showModal();
                    $scope.myForm.code = "";
                }
            } else {
                console.log("[goPlay] codigo invalido");
                
                $scope.text1 = "C\u00F3digo inv\u00E1lido";
                $scope.text2 = "Intenta con otro c\u00F3digo Cristal";

                $scope.showModal();
                $scope.myForm.code = "";
            }
        }, function(response) {
            console.log("[goPlay] error:", response);

            $scope.text1 = "¡Ops un error!";
            $scope.text2 = "Ha ocurrido un error inesperado, intentalo nuevamente";

            $scope.showModal();
            return false; 
        });

    }

    $scope.showModal = function() {
        $scope.$overlay.show();
        $scope.$modal.fadeIn("slow");
    };

    $scope.hideModal = function() {
        $scope.$overlay.hide();
        $scope.$modal.hide();
    };

    $scope.init = function() {
        console.log("[init] start", $cookies.getAll());

        $scope.name = $cookies.get("name");
        $scope.rut = $cookies.get("rut");
        $scope.email = $cookies.get("email");
        $scope.code = $cookies.get("code");
        $scope.code = $cookies.get("bar");

        $scope.$modal = $(".modal");
        $scope.$overlay = $(".modal-overlay");
    }

    $scope.init();
});

app.controller('playCtrl', function($scope, $http, $rootScope, $routeParams, $timeout, $interval, $location, $cookies){
    console.log("[playCtrl] start", $routeParams);
    $scope.params = $routeParams;

    $scope.goLogin = () => {
        $location.url("/bar/" + $scope.params.barId);
    }

    // CONFIG

    $scope.counterMax = 60;
    $scope.errorsMax = 10;

    // VARS

    $scope.counter = 0;
    $scope.match = 0;
    $scope.notMatch = 0;

    // COUNTER

    var stopped;

    $scope.timerRunning = false;

    $scope.countup = function() {
        $scope.timerRunning = true;
        stopped = $timeout(function() {
            $scope.counter++;
            if ($scope.counter >= $scope.counterMax) {
                $scope.end();
            } else {
                $scope.countup(); 
            }
        }, 1000); //1000 milliseconds = 1 second
    };

    $scope.stop = function(){
        $scope.timerRunning = false;
        $timeout.cancel(stopped);
    }; 

    // MEMORY

    $scope.shuffleCards = function(cardsArray){
        console.log("[shuffleCards] start");
        $scope.$cards = $($scope.shuffle($scope.cardsArray));
    };

    $scope.setup = function() {
        console.log("[setup] start");
        $scope.html = $scope.buildHTML();
        $scope.$game.html($scope.html);
        $scope.$memoryCards = $(".card-game");

        $scope.paused = false;
        $scope.guess = null;
    };

    $scope.binding = function() {
        console.log("[binding] start");
        $scope.$memoryCards.on("click", $scope.cardClicked);
        $scope.$restartButton.on("click", $.proxy($scope.reset, this));
    };

    // kinda messy but hey
    $scope.cardClicked = function() {
        var _ = $scope;
        var $card = $(this);

        if(!_.paused && !$card.find(".inside").hasClass("matched") && !$card.find(".inside").hasClass("picked")){
            $card.find(".inside").addClass("picked");
            
            if(!_.guess){
                console.log("[cardClicked] picked");
                _.guess = $(this).attr("data-id");
            } else if(_.guess == $(this).attr("data-id") && !$(this).hasClass("picked")){
                console.log("[cardClicked] match!");

                $scope.match++;
                
                $(".picked").addClass("matched");
                _.guess = null;
            } else {
                console.log("[cardClicked] not match");
                                
                $scope.notMatch++;
                console.log("[cardClicked] check", $scope.notMatch, $scope.errorsMax);
                if ($scope.notMatch >= $scope.errorsMax) {
                    $scope.end();
                }

                _.guess = null;
                _.paused = true;
                setTimeout(function(){
                    $(".picked").removeClass("picked");
                    $scope.paused = false;
                }, 600);
            }

            // win check
            if($(".matched").length == $(".card-game").length) {
                _.end();
            }
        }
    };

    $scope.end = function() {
        console.log("[end] start");

        $scope.stop(); // stop counter
        $scope.paused = true;

        $data = {
            code: $cookies.get("code"),
            name: $cookies.get("name"),
            rut: $cookies.get("rut"),
            email: $cookies.get("email"),
            code: $cookies.get("code"),
            bar: $scope.params.barId,
            time_elapsed: $scope.counter,
            wins: $scope.match
        };

        console.log("[end] data:", $data);

        $http({
            url: "api/tokens/", 
            method: "POST",
            data: $data
        }).then(function(response) {
            console.log("[end] success:", response);
            $data = response.data;
            if ($data.status == "OK") {
                
                $award = $data.award;
                console.log("[end] burned:", $award);

                // check awards 
                if ($award) {
                    setTimeout(function(){
                        $scope.text1 = "¡Ganaste!";
                        $scope.text2 = "Has ganado: " + $award + ". Con el código " + $cookies.get("code");
                        $scope.text3 = "Debes cobrarlo en los próximos 15 minutos acercandote a la barra y mostrando este resultado.";
                        setTimeout(function(){
                            $scope.showModal();
                            $scope.$game.fadeOut();
                        }, 1000);
                    }, 1000);
                } else {
                    $scope.text1 = "¡Sigue participando!";
                    $scope.text2 = "Intentalo nuevamente con otro código Cristal";
                    $scope.text3 = "";
                    setTimeout(function(){
                        $scope.showModal();
                        $scope.$game.fadeOut();
                    }, 1000);
                }
            } else {
                $scope.text1 = "¡Ops un error!";
                $scope.text2 = "Ha ocurrido un error inesperado, intentalo nuevamente";
                $scope.text3 = "Algo ha ocurrido en el servidor que no ha podido procesar tu solicitud.";
                setTimeout(function(){
                    $scope.showModal();
                    $scope.$game.fadeOut();
                }, 1000);
            }
        }, function(response) {
            console.log("[end] error:", response);
            $scope.text1 = "¡Ops un error!";
            $scope.text2 = "Ha ocurrido un error inesperado, intentalo nuevamente";
            $scope.text3 = "Verifica que tu conexión sea estable.";
            setTimeout(function(){
                $scope.showModal();
                $scope.$game.fadeOut();
            }, 1000);
        });
    };

    $scope.showModal = function() {
        $scope.$overlay.show();
        $scope.$modal.fadeIn("slow");
    };

    $scope.hideModal = function() {
        $scope.$overlay.hide();
        $scope.$modal.hide();
    };

    $scope.reset = function() {
        console.log("[reset] start");
        $scope.hideModal();
        $scope.shuffleCards(this.cardsArray);
        $scope.setup();
        $scope.$game.show("slow");
    };

    // Fisher--Yates Algorithm -- https://bost.ocks.org/mike/shuffle/
    $scope.shuffle = function(array) {
        console.log("[shuffle] start");

        var counter = array.length, temp, index;
        
        // While there are elements in the array
        while (counter > 0) {
            // Pick a random index
            index = Math.floor(Math.random() * counter);
            // Decrease counter by 1
            counter--;
            // And swap the last element with it
            temp = array[counter];
            array[counter] = array[index];
            array[index] = temp;
        }
        return array;
    };

    $scope.buildHTML = function() {
        console.log("[buildHTML] start");
        var frag = '';
        this.$cards.each(function(k, v){
            frag += '<div class="card-game" data-id="'+ v.id +'"><div class="inside">\
            <div class="front"><img src="'+ v.img +'"\
            alt="'+ v.name +'" /></div>\
            <div class="back img-carta"></div></div>\
            </div>';
        });
        return frag;
    };

    $scope.init = function(cards) {
        console.log("[init] start");

        // star counter
        $scope.countup();

        // star game
        $scope.$game = $(".game");
        $scope.$modal = $(".modal");
        $scope.$overlay = $(".modal-overlay");
        $scope.$restartButton = $("button.restart");
        $scope.cardsArray = $.merge(cards, cards);
        $scope.shuffleCards($scope.cardsArray);
        $scope.setup();
        $scope.binding();
    }

    $scope.cards = [
		{
			name: "php",
			img: "/img/cristalcero.png",
			id: 1,
		},
		{
			name: "css3",
			img: "/img/botella.png",
			id: 2
		},
		{
			name: "html5",
			img: "/img/camiseta.png",
			id: 3
		},
		{
			name: "jquery",
			img: "/img/logocristal.png",
			id: 4
		}, 
		{
			name: "javascript",
			img: "/img/lataradler.png",
			id: 5
		},
		{
			name: "node",
			img: "/img/latacristal.png",
			id: 6
		},
		{
			name: "photoshop",
			img: "/img/packcristal.png",
			id: 7
		},
		{
			name: "python",
			img: "/img/schop.png",
			id: 8
		},
		{
			name: "rails",
			img: "/img/pelota.png",
			id: 9
		}		
	];

    $scope.init($scope.cards);
});

app.filter('formatTimer', function() {
    return function(input) {
        function z(n) {return (n<10? '0' : '') + n;}
        var seconds = input % 60;
        var minutes = Math.floor(input / 60);
        
        // MM:SS
        return (+z(minutes)+':'+z(seconds));

        // HH:MM:SS
        //var hours = Math.floor(minutes / 60);
        //return (z(hours) +':'+z(minutes)+':'+z(seconds));
    };
});