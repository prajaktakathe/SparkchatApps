var httpDomain = "http://107.170.130.230:8443";
var userDetailUrl = "/plugins/sparkchat/api/v1/user/list/15";
var app = angular.module('myApp', ['ui.router','ngTagsInput','ui.bootstrap']);
var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var replaceHtmlEntites = (function() {
    var translate_re = /&(nbsp|amp|quot|lt|gt);/g,
        translate = {
            'nbsp': String.fromCharCode(160),
            'amp' : '&',
            'quot': '"',
            'lt'  : '<',
            'gt'  : '>'
        },
        translator = function($0, $1) {
            return translate[$1];
        };

    return function(s) {
        return s.replace(translate_re, translator);
    };
})()
app.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
  $urlRouterProvider.otherwise('/task');
    $stateProvider
    .state('task', {
              url : '/task?userId&token&teamId',
              templateUrl : 'partials/task.html',
              controller : 'tasksController'
            })
    .state('edit', {
                url: '/task/edit?id?uId?token?teamId?teamDomain',
                templateUrl: 'partials/edit_task.html',
                controller : 'EditTaskController'
            })
    .state('task.list', {
          url: '/{fold}'
      });

  }]);

function getCurrentTimeStamp(){
  var date = new Date();
  var currentTime = date.getTime() / 1000;
  return Math.floor(currentTime);
}
function generateUid() {
  var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
    var r = Math.random() * 16 | 0,
      v = (c === 'x') ? r : r & 0x3 | 0x8;
    return v.toString(16);
  });
  return uuid;
}
app.controller('tasksController', function($scope, $http,$state,$location,$modal) {
 // Load all available tasks
  $scope.all = true;
  $scope.statusList = ["1","2","3"];
  $scope.filterList = ["All","Due Date"];
  $scope.taskFilter = $scope.filterList[0];
  $scope.orderList = ["Select","Due Date","Priority"];
  $scope.orderType = ["isCompleted","-startTimeStamp"];
  $scope.taskOrder = $scope.orderList[0];
  $scope.sorter = "null";
  $scope.sortDescending = false;
  var user = $location.search();
  $scope.userId = user.uId;
  $scope.resource = user.token;
  $scope.teamId = user.teamId;
  $scope.teamDomain = user.teamDomain;
//  console.log(user);
//  console.log($scope.resource);
//  console.log($scope.teamId);
  getTask();
  // var requestParam = $.param({
	// 	    	uId : $scope.userId,
	// 	    	token : $scope.resource,
  //         teamId : $scope.teamId
	// 	    });
  // var uid = 'c084520693528060e6cd29f2ac9e0f40';
  // var token ='2bd81766-9dbd-475e-bafc-47b5189481b3';
  // var base64 = btoa(uid+":"+token);
		    // $http({
		    //   method: "post",
		    //   url: httpDomain + userDetailUrl,
		    //   data: requestParam
		    // }).success(function(data, status, headers, config) {
		    // 	console.log(JSON.stringify(data));
		    // }).error(function(data, status, headers, config) {
		    // 	console.log(data);
		    // });
  $scope.parseId = function(val){
    val.isCompleted = parseInt(val.isCompleted);
    val.isImportant = parseInt(val.isImportant);
  }
  // $scope.team = $scope.teamDomain;
  $scope.createTask = function () {
  //  console.log("hello 1");
  //  console.log($scope.titleInput);
	    	if (typeof $scope.titleInput != "undefined" && $scope.titleInput != "") {
	    		var currentTime = getCurrentTimeStamp();
	    		var uniqueId = generateUid() + "-" + currentTime;
	    		//console.log("hello 2");
          addTask($scope.titleInput,$scope.teamDomain);
	    	}

	    }

  function getTask(){
    console.log($scope.userId);
    var requestParam = $.param({
  		    	team : $scope.teamDomain,
            uId : $scope.userId
  		    });
       var test=$.ajax({
         "url": "ajax/getTask.php",
         "method": "GET",
         "data": requestParam,
         "success":function success(data) {
           $scope.taskList = JSON.parse(data);
          // console.log($scope.taskList);
           $scope.$apply();
         	if ($scope.taskList == null) {
         		$scope.taskList = [];
         	}
         }
       });
  };

  function addTask (task,team) {
    $scope.id= generateUid();
    var requestParam = $.param({
            task_id:$scope.id,
  		    	task : task,
  		    	team : team,
            assign_by:$scope.userId
  		    });
    var test=$.ajax({
      "url": "ajax/addTask.php",
      "method": "GET",
      "data": requestParam,
      "success":function success(data) {
      //  console.log(data);
        $scope.titleInput="";
         getTask();
      }
    });
  };
  $scope.addStarClass = function (isImportant) {
    	if (isImportant == 1) {
    		return 'glyphicon-star';
    	} else {
    		return 'glyphicon-star-empty';
    	}
    }
  // $scope.deleteTask = function (task) {
  //   if(confirm("Are you sure to delete this line?")){
  //   $http.post("ajax/deleteTask.php?taskID="+task).success(function(data){
  //       getTask();
  //     });
  //   }
  // };
  //
  $scope.updateTaskStatus = function( status,item) {
  //  console.log("hello");
  //  console.log("status : "+status);
      var requestParam = $.param({
              task_id:item,
              status : status,
              team : $scope.teamDomain
            });
      var test=$.ajax({
        "url": "ajax/updateTask.php",
        "method": "GET",
        "data": requestParam,
        "success":function success(data) {
        //  console.log(data);
           $scope.$apply();
           getTask();
        }
      });
  };
  $scope.toggleImportant = function(isImportant, taskId) {
    if(isImportant=='1'){isImportant='0';}else{isImportant='1';}
    var requestParam = $.param({
            task_id:taskId,
            priority : isImportant,
            team : $scope.teamDomain
          });
    var test=$.ajax({
      "url": "ajax/updateTask.php",
      "method": "GET",
      "data": requestParam,
      "success":function success(data) {
      //  console.log(data);
         $scope.$apply();
         getTask();
      }
    });
  }
  $scope.showAll = function(){
    $scope.all = true;
    $scope.active = false;
    $scope.complete = false;
  };
  $scope.showActive = function(){
    $scope.all = false;
    $scope.active = true;
    $scope.complete = false;
  }
  $scope.showComplete = function(){
    $scope.all = false;
    $scope.active = false;
    $scope.complete = true;
  }

  $scope.openEditModal = function($event, taskId, status) {
		var taskArray = $scope.taskList;
		var clickedElement = $event.target;
		var elementClasses = clickedElement.classList;
		//customLog($scope.controllerName,"element class : "+elementClasses);
		if (elementClasses.contains('row') || elementClasses.contains('list-group-item')
			|| elementClasses.contains('title-container') || elementClasses.contains('todo-parent')) {
			var taskDetail = "";
			for (var i = 0; i < taskArray.length; i++) {
				if (taskArray[i].task_id == taskId) {
					taskDetail = taskArray[i];
					break;
				};
			};
			$state.go('edit',{id: taskId,uId: $scope.userId,token: $scope.resource,teamId: $scope.teamId,teamDomain: $scope.teamDomain});
		};

	}
  $scope.updateOrder = function() {
			if ($scope.taskOrder == "Priority") {
			$scope.sortDescending = true;
			//$scope.sorter = ["isImportant","-endTimeStamp"];
			$scope.orderType = ["isCompleted","-isImportant","endTimeStamp","-startTimeStamp"];
		} else {
			if ($scope.taskOrder == "Due Date") {
				$scope.sorter = "endTimeStamp";
				$scope.sortDescending = false;
				$scope.orderType = ["isCompleted","endTimeStamp","-isImportant","-startTimeStamp"];
			} else {
				$scope.orderType = ["isCompleted","-startTimeStamp"];
				$scope.sortDescending = false;
				$scope.sorter = 'null';
			}
		}
		}
});
app.controller('EditTaskController', ['$rootScope','$scope', '$location', '$http', '$state', '$window', '$modal','$q', function($rootScope,$scope, $location, $http, $state, $window, $modal,$q) {
  var user = $location.search();
  $scope.userId = user.uId;
  $scope.resource = user.token;
  $scope.teamId = user.teamId;
  $scope.teamDomain = user.teamDomain;

    function getUsers(){
    //  console.log("hello");
       $scope.id= generateUid()+"-"+getCurrentTimeStamp();
       var requestParam = $.param({
               team:$scope.teamDomain
             });
       var test=$.ajax({
         "url": "ajax/getUsers.php",
         "method": "GET",
         "data": requestParam,
         "success":function success(data) {
           $scope.users= JSON.parse(data);
            $scope.usernames=[];
           //console.log($scope.users);
           for (var i = 0; i < $scope.users.length; i++) {
             $scope.usernames.push({
               id: $scope.users[i].user_name,
               name:$scope.users[i].name
             });
           }
          // console.log($scope.usernames);
           $scope.$apply();
         }
       });
   }
    getUsers();
     $scope.loadTags = function(query) {
       //console.log($scope.usernames);
        var deferred = $q.defer();
        deferred.resolve($scope.usernames);
        return deferred.promise;
      };

  	$scope.format = 'dd-MMMM-yyyy';
  	$scope.formData = {};
  	$scope.reminderHour = "0";
  	$scope.input = {};
  	$scope.showError = null;
  	$scope.showReminder = false;
  	$scope.showReminder1 = false;
  	$scope.showReminder2 = false;
  	$scope.showReminder4 = false;
  	$scope.mindate = new Date();
  	$scope.uniqueKey  = "";
  	var lastUpdatedSubTask = null;
  	$scope.reminderOptionList = ["None","1 hour Before","2 hour Before","4 hour Before"];
  	//$scope.selectedReminder = $scope.reminderOptionList[0];
  	var taskId = $location.search().id;
  //  console.log(taskId);
    getSingleTask();

    $scope.parseId = function(val){
      val.subtaskStatus = parseInt(val.subtaskStatus);
      val.subtaskPriority = parseInt(val.subtaskPriority);
    }

    function getSingleTask(){
      var requestParam = $.param({
    		    	team : $scope.teamDomain,
              taskId : taskId
    		    });
         var test=$.ajax({
           "url": "ajax/getSingleTask.php",
           "method": "GET",
           "data": requestParam,
           "success":function success(info) {
             console.log(info);
             $scope.data = JSON.parse(info);
             console.log($scope.data[0].subTasks);
            console.log($scope.data[0].assign_to);
             if($scope.data[0].subTasks.length != 0 && $scope.data[0].subTasks != " " ){
                 $scope.subTasks = JSON.parse($scope.data[0].subTasks);
             }else {
               $scope.subTasks="";
             }

             if($scope.data[0].assign_to.length != 0 && $scope.data[0].assign_to != " "){
                 $scope.tags = JSON.parse($scope.data[0].assign_to);
             }else {
               $scope.tags="";
             }
          //  $scope.subTasks = JSON.parse($scope.data[0].subTasks);
            // console.log($scope.subTasks);
            //  console.log($scope.tags);

          	var referenceTitle;

          	//increment step for time
              $scope.hstep = 1;
              $scope.mstep = 1;
              //status for reminder
              $scope.reminderStatus = {
              	isopen: false
              };
          	$scope.datepickers = {
                  dt: false,
                  dtSecond: false
              }
          	$scope.uniqueKey = $scope.data[0].task_id;
          	$scope.startTimeStamp = $scope.data[0].startTimeStamp;
          	$scope.createdBy = $scope.data[0].created_by;
          	$scope.assignTo = $scope.data[0].assign_to;
          	$scope.input.title = $scope.data[0].title;
            // console.log($scope.input.title);
          	if ($scope.data[0].description != " ") {
          		$scope.input.description = $scope.data[0].description;
          	} else {
          		$scope.input.description = undefined;
          	}

          	$scope.isImportant = $scope.data[0].isImportant;
          	$scope.isCompleted = $scope.data[0].isCompleted;
          	//$scope.firstTime = new Date(data.startTimeStamp * 1000);
          	if ($scope.data[0].endTimeStamp != 9999999999) {
          		$scope.secondTime = new Date($scope.data[0].endTimeStamp*1000);
          	} else {
          		$scope.secondTime = new Date();
          	}
          	if ($scope.data[0].endTimeStamp != 9999999999) {
          		var date2 = new Date($scope.data[0].endTimeStamp*1000);
          		var sdYear = date2.getFullYear();
          		var sdMonth = date2.getMonth();
          		var sdDay = date2.getDate();
          		$scope.formData.dtSecond = sdDay+"-"+monthNames[sdMonth]+"-"+sdYear;
          	}
          	if ($scope.data[0].reminder == 0) {
          		$scope.selectedReminder = $scope.reminderOptionList[0];
              	$scope.reminderHour = "0";
          	}else {
          		if ($scope.data[0].reminder == 1) {
              		$scope.selectedReminder = $scope.reminderOptionList[1];
              		$scope.reminderHour = "1";
              	} else {
              		if ($scope.data[0].reminder == 2) {
              			$scope.selectedReminder = $scope.reminderOptionList[2];
              			$scope.reminderHour = "2";
              		} else {
              			if ($scope.data[0].reminder == 4) {
              				$scope.selectedReminder = $scope.reminderOptionList[3];
              				$scope.reminderHour = "4";
              			};
              		}
              	}
          	}
            $scope.$apply();
           }
         });
       };
       /**
        * use to watch Due date picker
       */
       $scope.$watch('formData.dtSecond', function (newValue, oldValue){
           if (typeof newValue == "undefined") {
             $scope.showReminder = false;
             $scope.selectedReminder = $scope.reminderOptionList[0];
             $scope.reminderHour = "0";
           } else {
             $scope.showReminder = true;
             !$scope.reminderStatus.isopen
             var endDate = Math.floor((new Date(newValue)).getTime()/1000);
           var endTime = new Date($scope.secondTime);
           var endHour = endTime.getHours();
           var endMinute = endTime.getMinutes();
           var endSecond = endTime.getSeconds();
           $scope.fullDueDate  = endDate + (endHour * 3600 + endMinute * 60 + endSecond);

           //for alreadey exist reminder
           if ($scope.fullDueDate - 3600 * $scope.reminderHour < getCurrentTimeStamp()) {
             $scope.selectedReminder = $scope.reminderOptionList[0];
               $scope.reminderHour = "0";
           };

           }
         });
         $scope.addSubTask = function () {
           console.log("hello");
          console.log($scope.input.subTask);
        		if (typeof $scope.input.subTask != "undefined" && $scope.input.subTask != " ") {
            $scope.id= generateUid()+"-"+getCurrentTimeStamp();
            var requestParam = $.param({
                    team:$scope.teamDomain,
                    taskId:taskId,
                    subtaskId:$scope.id,
          		    	subtaskTitle : $scope.input.subTask,
                    subtaskStatus : 0,
                		subtaskPriority : 0
          		    });
            var test=$.ajax({
              "url": "ajax/addSubTask.php",
              "method": "GET",
              "data": requestParam,
              "success":function success(data) {
                console.log(data);
                $scope.input.subTask = "";
                $scope.$apply();
                 getSingleTask();
              }
            });
        	}
        }


        $scope.deleteTask = function () {
      		try {
            var requestParam = $.param({
                    team:$scope.teamDomain,
                    taskId:taskId
                  });
            var test=$.ajax({
              "url": "ajax/deleteTask.php",
              "method": "GET",
              "data": requestParam,
              "success":function success(data) {
                //console.log(data);
                $scope.$apply();
                 $window.history.back();
                //  getSingleTask();
              }
            });
      		} catch (err) {
      			//console.log("Error inside onDelete() : ");
      			//console.log(err);
      		}
      	}
  $scope.cancel = function () {
		//console.log("inside onCancel() : ");
		$window.history.back();
   }

   $scope.open = function($event, which) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.datepickers[which]= true;
    };
    $scope.dateOptions = {
      formatYear: 'yy',
      startingDay: 1,
      class: 'datepicker'
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function() {
      $scope.ismeridian = ! $scope.ismeridian;
    };

    //toggle dropdown for reminder
    $scope.toggleDropdown = function($event) {
      $event.preventDefault();
      $event.stopPropagation();
      $scope.reminderStatus.isopen = !$scope.reminderStatus.isopen;
    };



    $scope.updateReminder = function () {
    	switch($scope.selectedReminder) {
    		case $scope.reminderOptionList[0]:
    			//$scope.se = 'None';
    			$scope.reminderHour = "0";
    			break;
    		case $scope.reminderOptionList[1]:
    			//$scope.reminderOption = 'Before 1 hour';
    			$scope.reminderHour = "1";
    			if ($scope.fullDueDate - 3600 * 1 < getCurrentTimeStamp()) {
    				$scope.showErrorModal("Unable to set reminder as due date is less then current time ");
    				$scope.selectedReminder = $scope.reminderOptionList[0];
	    		}
    			break;
    		case $scope.reminderOptionList[2]:
    			//$scope.reminderOption = 'Before 2 hours';
    			$scope.reminderHour = "2";
    			if ($scope.fullDueDate - 3600 * 2 < getCurrentTimeStamp()) {
    				$scope.showErrorModal("Unable to set reminder as due date is less then current time ");
    				$scope.selectedReminder = $scope.reminderOptionList[0];
	    		}
    			break;
    		case $scope.reminderOptionList[3]:
    			//$scope.reminderOption = 'Before 4 hours';
    			$scope.reminderHour = "4";
    			if ($scope.fullDueDate - 3600 * 4 < getCurrentTimeStamp()) {
    				$scope.showErrorModal("Unable to set reminder as due date is less then current time ");
    				$scope.selectedReminder = $scope.reminderOptionList[0];
	    		}
    			break;
    	}

    }

    $scope.changedFirstTime = function(value) {
    	$scope.firstTime = value;
    	//customLog($scope.controllerName,"On Model change : "+$scope.firstTime);
    }

    $scope.changedSecondTime = function(value) {
    	$scope.secondTime = value;
    	//customLog($scope.controllerName,"On Model change : "+$scope.formData.dtSecond);
    	if ( typeof $scope.formData.dtSecond != "undefined") {
    		var endDate = Math.floor((new Date($scope.formData.dtSecond)).getTime()/1000);
			var endTime = new Date($scope.secondTime);
			var endHour = endTime.getHours();
			var endMinute = endTime.getMinutes();
			var endSecond = endTime.getSeconds();
			$scope.fullDueDate = endDate + (endHour * 3600 + endMinute * 60 + endSecond);
			//for alreadey exist reminder
			if ($scope.fullDueDate - 3600 * $scope.reminderHour < getCurrentTimeStamp()) {
				$scope.selectedReminder = $scope.reminderOptionList[0];
    			$scope.reminderHour = "0";
			};
    	};
    }

    $scope.saveTask = function () {
      console.log("inside fun");
    		try {
    			$scope.showError = null;
    			var endTimeStamp = 9999999999999;
    			if (typeof $scope.input.description == "undefined") {
    				$scope.input.description = " ";
    			}
    			var isValid = validateInput();
    			if (isValid) {
            if (typeof $scope.formData.dtSecond != "undefined") {
      				var endDate = (new Date($scope.formData.dtSecond)).getTime();
      				var endTime = new Date($scope.secondTime);
      				var endHour = endTime.getHours();
      				var endMinute = endTime.getMinutes();
      				var endSecond = endTime.getSeconds();
      				var endTimeStamp = endDate + (endHour * 3600 + endMinute * 60 + endSecond) * 1000;
      			}
              console.log("inside try");
                var currentTime = getCurrentTimeStamp();
                var tags = JSON.parse(angular.toJson($scope.tags));
                var subTasks = JSON.parse(angular.toJson($scope.subTasks));
                var requestParam = $.param({
                        team:$scope.teamDomain,
                        taskId:taskId,
                        title:$scope.input.title,
                        description:$scope.input.description,
                        endTimeStamp:Math.floor(endTimeStamp/1000),
                        reminder:parseInt($scope.reminderHour),
                        assignTo:tags,
                        subTasks:subTasks,
                        lastModified:currentTime
                      });
                var test=$.ajax({
                  "url": "ajax/updateTask.php",
                  "method": "GET",
                  "data": requestParam,
                  "success":function success(data) {
                    console.log(data);
                    $scope.$apply();
                    //  getSingleTask();
                  }
                });
                $window.history.back();
                }
    		} catch (err) {
    			console.log("Error inside saveTask : "+err.message);
    		}
    	}

      $scope.tagRemoved = function(tag) {
        console.log('Tag removed: ', tag.id);
        try {
          var requestParam = $.param({
                  team:$scope.teamDomain,
                  taskId:taskId,
                  userId:tag.id
                });
          var test=$.ajax({
            "url": "ajax/deleteTags.php",
            "method": "GET",
            "data": requestParam,
            "success":function success(data) {
            //  console.log(data);
              $scope.$apply();
            //  getSingleTask();
            }
          });
        } catch (err) {
          console.log("Error inside onDelete() : ");
          console.log(err);
        }
      };
      $scope.whenFocus = function (title) {
    		referenceTitle = new String(title);
    	}

    	$scope.whenLoseFocus = function(title, uniqueId) {
    		if (title == "") {
    			var lastFocusedTitle = null;
    			for (var i = 0; i < $scope.subTasks.length; i++) {
    				if ($scope.subTasks[i].subtaskId == uniqueId) {
    					if (referenceTitle != null) {
    						$scope.subTasks[i].subtaskTitle = referenceTitle;
    						break;
    					};
    				}
    			};
    		};
    	}
      $scope.addSubTaskStarClass = function (isImportant) {
    		if (isImportant) {
        		return 'glyphicon-star';
        	} else {
        		return 'glyphicon-star-empty';
        	}
    	}
      $scope.removeSubTask = function (subTask) {
    		//$scope.subTasks.splice($scope.subTasks.indexOf(subTask), 1);
        var requestParam = $.param({
                subtask_id:subTask,
                status : status,
                team : $scope.teamDomain,
                taskId:taskId,
              });
        var test=$.ajax({
          "url": "ajax/deleteSubTask.php",
          "method": "GET",
          "data": requestParam,
          "success":function success(data) {
          //  console.log(data);
             $scope.$apply();
            getSingleTask();
          }
        });
    	}

      $scope.updateSubTaskStatus = function(status,item) {
       console.log("status : "+status);
          var requestParam = $.param({
                  subtask_id:item,
                  status : status,
                  team : $scope.teamDomain,
                  taskId:taskId,
                });
          var test=$.ajax({
            "url": "ajax/updateSubTask.php",
            "method": "GET",
            "data": requestParam,
            "success":function success(data) {
            //  console.log(data);
               getSingleTask();
            }
          });
      };

      $scope.toggleSubImportant = function (isImportant, subTask){
        console.log("priority : "+isImportant);
        console.log("priority : "+subTask);
          if(isImportant=='1'){isImportant='0';}else{isImportant='1';}
          var requestParam = $.param({
                  taskId:taskId,
                  priority : isImportant,
                  team : $scope.teamDomain,
                  subtask_id:subTask,
                });
          var test=$.ajax({
            "url": "ajax/updateSubTask.php",
            "method": "GET",
            "data": requestParam,
            "success":function success(data) {
            //  console.log(data);
               $scope.$apply();
               getSingleTask();
            }
          });
    	}
    	$scope.disableEnter = function ($event) {
    		if ($event.which == 13) {
    			$event.preventDefault();
    			return false;
    		}
    	}

    function validateInput() {
    		if (typeof $scope.input.title == "undefined" || $scope.input.title == "") {
    			$scope.showError = 'Title should not be blank';
    			return false;
    		}
    		return true;
    	}
}]);
app.directive("contenteditable", function() {
  	return {
	    restrict: "A",
	    require: "ngModel",
	    link: function(scope, element, attrs, ngModel) {

	    	function read() {
		      	var html = element.html();
		        html = replaceHtmlEntites(html);
		        ngModel.$setViewValue(html);
	      	}
	      	ngModel.$render = function() {
	        	element.html(ngModel.$viewValue || "");
	      	};

	      	element.bind("blur keyup change", function(event) {
	        	if (event.which == 13) {

	        	} else {
	        		scope.$apply(read);
	        	}
	      	});
	    }
  	};
});
app.directive('customEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if (event.which === 13) {
                scope.$apply(function () {
                    scope.$eval(attrs.ngEnter);
                });
                event.preventDefault();
            }
        });
    };
});
