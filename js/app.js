"use strict";

var app = angular.module("game", ["ngRoute", "ngResource"]);

app.config(function($routeProvider)
{
    $routeProvider
    .when("/page/:name", {
        templateUrl : function(page){
        	return "assets/"+page.name+".html"
        },
        controller: "pagesController"
    })
    .otherwise("/page/start");
})


.controller("pagesController",function($scope, $http){

	$http.get("?controller=user").success(function (data) {
		$scope.users = data;
	});
})


.controller("gameController", function ($scope, $rootScope, $http, $location) {

	$scope.level = 0;
	$scope.health = 100;
	$scope.score = 0;

	$rootScope.$on('caughtZombie', function () {
		$scope.level++;
		$scope.score += parseInt($rootScope.currentPower);
	});

	$rootScope.$on('notCaughtZombie', function () {

		if (($scope.health - 2 * parseInt($rootScope.currentPower)) <= 0)
		{
			$scope.health = 0;
			$rootScope.$emit('endGame');
		}
		else
		{
			$scope.level++;
			$scope.health -= 2 * parseInt($rootScope.currentPower);
		}
	});

	$rootScope.$on('endZombie', function () {
		$rootScope.$emit('endGame');
	});

	$rootScope.$on('endGame', function () {
		$rootScope.totalScore = $scope.score;
		if($scope.score <= 30){
			$location.path('/page/game_over_lose');
		}else $location.path('/page/game_over');
	});

	$scope.sendData = function () {
		$http.post("?controller=user",
			{id:0, name: $scope.entername.username.$modelValue, score: $rootScope.totalScore})
			.success(function () {
				$location.path('/page/start');
			})
	}
})


.controller("menuController", function ($scope, $http) {
	$http.get("?controller=menu").success(function (data) {
		$scope.items = data;
	});
})


.controller("zombieController", function ($http, $scope, $interval, $timeout, $rootScope) {

	var zombies;

	$http.get("?controller=zombie").success(function (data) {
		zombies = data;
		$scope.next();
	});

	var current = 0;
	var isEnd = false;
	$scope.power = 0;
	$scope.speed = 1;
	$scope.image = "images/zombies/1.png";
	$scope.active = "hidden";

	var timer;
	var timeBorder = 0;
	$scope.x = 50;
	

	var animation, time = 0;

	$scope.move = function ()
    {
		$scope.active = "visible";
		animation = $interval(function () {
			time += parseInt($scope.speed) / 8;
			$scope.x = 40 * (time / 40);
			$scope.y = 15 * Math.sin(time / 10) + 5;
		}, 30);
		
		timeBorder += 1000;
		
		timer = $timeout(function () {
			$scope.notCaught();
		}, 10000 - timeBorder)
	};
	

	$scope.notCaught = function () {
		$rootScope.$emit('notCaughtZombie');
		$scope.stop();
	};

	$scope.caught = function () {
		$rootScope.$emit('caughtZombie');
		$timeout.cancel(timer);
		$scope.stop();
	};

	$scope.stop = function () {
		$scope.active = "hidden";
		$interval.cancel(animation);
		time = 0;
		$scope.x = 50;
		$scope.y = 20;

		if (!isEnd)
			$scope.next();
	};

	$scope.next = function () {

		if (current < zombies.length)
		{
			$scope.power = zombies[current].power;
			$rootScope.currentPower = $scope.power;
			$scope.speed = zombies[current].speed;
			$scope.image = zombies[current].image;

			current++;

			$scope.move();
		}
		else
		{
			$rootScope.$emit('endZombie');
		}
	};

	$rootScope.$on('endGame', function () {
		isEnd = true;
	});
})


.directive("header", function(){
	return {
		templateUrl:"assets/directives/header.html",
		replace: true,
		restrict: 'E',
		scope:{},
		controller: "menuController"
	}
})


.directive("zombie", function () {
	return {
		templateUrl:"assets/directives/zombie.html",
		replace: true,
		restrict: 'E',
		scope:{},
		controller: "zombieController"
	}
})