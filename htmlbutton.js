/*
 *  handles html button clicks in the interface - very similar to keyboardteleop.js
 */

var HTMLBUTTON = HTMLBUTTON || {
  REVISION : '1'
};

/**
 * @author John G. Raiti
 */

HTMLBUTTON.Teleop = function(options) {
  var that = this;
  options = options || {};
  var ros = options.ros;
  var topic = options.topic || '/cmd_vel';
  // permanent throttle
  var throttle = options.throttle || 1.0;

  var takeoff = new ROSLIB.Topic({
      ros : ros,
      name : '/ardrone/takeoff',
      messageType : 'std_msgs/Empty'
    });

  var land = new ROSLIB.Topic({
      ros : ros,
      name : '/ardrone/land',
      messageType : 'std_msgs/Empty'
    });

  var head = new ROSLIB.Topic({
    ros : ros,
    name : '/r2/r2_controller/left_arm/joint_command',	
    messageType : 'sensor_msgs/JointState'
  });
    var neckJointNames = new ROSLIB.Message({
    //  frame_id : 'r2/robot_base',      
      name : 'neckJointNames',
      position : [0.0],
      velocity : [0],
      effort : [-5.0]
    });


	document.getElementById("head").onclick=function(){
		head.publish(neckJointNames);
		//console.log('head button');
	};

//	document.getElementById("land").onclick=function(){
//		land.publish();
		//console.log('land button');
//	};

  // used to externally throttle the speed (e.g., from the distance away from the center of the image)
  this.scale = 1.0;

  // linear x and y movement and angular z movement
  var xx = 0;
  var yy = 0;
  var zz = 0;
  var upp = 0;

  var cmdVel = new ROSLIB.Topic({
    ros : ros,
    name : topic,
    messageType : 'geometry_msgs/Twist'
  });
  
  // sets up a key listener on the page used for html button teleoperation
  // keyDown is true when there is a mouse click in the video window
  var handleClick = function(clickDown) {
    // used to check for changes in speed
    var oldX = xx;
    var oldY = yy;
    var oldZ = zz;
    var oldUp = upp;

    var speed = 0;
    // throttle the speed by the slider and throttle constant
    if (clickDown === true) {
      speed = throttle * that.scale;
    }
    // check which way to go
      if (left = true){
        zz = 1 * speed; 
	}
      if (forward = true){
        xx = 1 * speed; 
	}
      if (right = true){
        zz = -1 * speed; 
	}
      if (up = true){
        upp = 1 * speed;
	zz = 0; 
	}
      if (down = true){
        upp = -1 * speed; 
	}


    // publish the command
    var twist = new ROSLIB.Message({
      angular : {
        x : 0,
        y : 0,
        z : zz
      },
      linear : {
        x : xx,
        y : yy,
        z : upp
      }
    });
    cmdVel.publish(twist);

    // check for changes
    if (oldX !== xx || oldY !== yy || oldZ !== zz || oldUp != upp) {
      that.emit('change', twist);
      console.log('throttle change:  '+ oldX + oldY + oldZ + oldUp);
    }
  };
/*
  // handle the click
  document.getElementById("background").addEventListener('mousedown', function(e) {
	//on mousedown in the video stream, do the following:

    	var go = e.target.id;
	handleClick(true);
 	var x = e.pageX - 50 - (document.getElementById("liveVideo").offsetWidth/2);  //document.getElementById("background").offsetLeft
        var y = e.pageY - document.getElementById("background").offsetTop - (document.getElementById("liveVideo").offsetHeight/2);
	//console.log('mousedown:position: x: '+e.clientX+' y: '+e.clientY+' moved to: X: '+x+' Y: '+y+' offsetLeft:'+document.getElementById("background").offsetLeft);


	if (go == F) { //go forward
		forward = true; }
	else if (go == D) { //go down
		down = true;
		document.getElementById("downhighlight").style.backgroundColor = "greenyellow"; }
	else if (go == U) { //go up!
		up = true;
		document.getElementById("uphighlight").style.backgroundColor = "greenyellow"; }
	else if (go == L) { //go left
		left = true; 
		document.getElementById("lefthighlight").style.backgroundColor = "greenyellow";}
	else if (go == R) { //go right
		right = true; 
		document.getElementById("righthighlight").style.backgroundColor = "greenyellow";}
	else if (go == DL) { //go down and left
		down = true;
		left = true; 
		document.getElementById("downhighlight").style.backgroundColor = "greenyellow";
		document.getElementById("lefthighlight").style.backgroundColor = "greenyellow"}
	else if (go == DR) { //go down and right
		down = true;
		right = true; 
		document.getElementById("downhighlight").style.backgroundColor = "greenyellow";
		document.getElementById("righthighlight").style.backgroundColor = "greenyellow"}
	else if (go == UL) { //go up! and left
		up = true;
		left = true; 
		document.getElementById("lefthighlight").style.backgroundColor = "greenyellow";
		document.getElementById("uphighlight").style.backgroundColor = "greenyellow"; }
	else if (go == UR) { //go up! and right
		up = true;
		right = true; 
		document.getElementById("righthighlight").style.backgroundColor = "greenyellow";
		document.getElementById("uphighlight").style.backgroundColor = "greenyellow";}
	console.log('X:'+x+' Y:'+y+'  forward='+forward+'  up='+up+'  down='+down+'  left='+left+'  right='+right);
	//console.log(e.pageY);
	//console.log(document.getElementById("background").offsetTop);
	//console.log(document.getElementById("liveVideo").offsetHeight/2);
	
	if ((x >=-100 && x<=100) && (y>=-75 && y<=75)) { //go forward
		forward = true; }
	else if ((x >=-100 && x<=100) && (y>75)) { //go down
		down = true;
		document.getElementById("downhighlight").style.backgroundColor = "greenyellow"; }
	else if ((x >=-100 && x<=100) && (y<-75)) { //go up!
		up = true;
			document.getElementById("uphighlight").style.backgroundColor = "greenyellow"; }
	else if ((x<-100) && (y>=-75 && y<=75)) { //go left
		left = true; 
		document.getElementById("lefthighlight").style.backgroundColor = "greenyellow";}
	else if ((x>100) && (y>=-75 && y<=75)) { //go right
		right = true; 
		document.getElementById("righthighlight").style.backgroundColor = "greenyellow";}
	else if ((x<-100) && (y>75)) { //go down and left
		down = true;
		left = true; 
		document.getElementById("downhighlight").style.backgroundColor = "greenyellow";
		document.getElementById("lefthighlight").style.backgroundColor = "greenyellow"}
	else if ((x >100) && (y>75)) { //go down and right
		down = true;
		right = true; 
		document.getElementById("downhighlight").style.backgroundColor = "greenyellow";
		document.getElementById("righthighlight").style.backgroundColor = "greenyellow"}
	else if ((x<-100) && (y<-75)) { //go up! and left
		up = true;
		left = true; 
		document.getElementById("lefthighlight").style.backgroundColor = "greenyellow";
		document.getElementById("uphighlight").style.backgroundColor = "greenyellow"; }
	else if ((x >100) && (y<-75)) { //go up! and right
		up = true;
		right = true; 
		document.getElementById("righthighlight").style.backgroundColor = "greenyellow";
		document.getElementById("uphighlight").style.backgroundColor = "greenyellow";}
	console.log('X:'+x+' Y:'+y+'  forward='+forward+'  up='+up+'  down='+down+'  left='+left+'  right='+right);
	//console.log(e.pageY);
	//console.log(document.getElementById("background").offsetTop);
	//console.log(document.getElementById("liveVideo").offsetHeight/2);

  }, false);

  var body = document.getElementsByTagName('body')[0]; 
  body.addEventListener('mouseup', function(e) {      
    handleClick(false);
    console.log('mouseup: '+ e.target.id);
	var forward = false;
	var up = false;
	var down = false;
	var left = false;
	var right = false;
	document.getElementById("downhighlight").style.backgroundColor = "white";
	document.getElementById("uphighlight").style.backgroundColor = "white";
	document.getElementById("lefthighlight").style.backgroundColor = "white";
	document.getElementById("righthighlight").style.backgroundColor = "white";
  }, false);

  //body.addEventListener('load', function(e) {      
  body.addEventListener('mouseover', function(e) {      
	document.getElementById("background").style.transformOrigin="320 180" ;
    	console.log('transformed origin');
	// initialize
	var forward = false;
	var up = false;
	var down = false;
	var left = false;
	var right = false;
	document.getElementById("downhighlight").style.backgroundColor = "white";
	document.getElementById("uphighlight").style.backgroundColor = "white";
	document.getElementById("lefthighlight").style.backgroundColor = "white";
	document.getElementById("righthighlight").style.backgroundColor = "white";
  }, false);
*/

};


HTMLBUTTON.Teleop.prototype.__proto__ = EventEmitter2.prototype;

