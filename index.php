<?php
/**
 * NASA Robonaut Interface
 *
 * @author     John Raiti <john_raiti@brown.edu>
 * @copyright  2014 Brown University
 * @license    BSD -- see LICENSE file
 * @version    Apr, 8 2014
 */

/**
 * A static class to contain the interface generate function.
 */
class robonaut_interface
{
    /**
     * Generate the HTML for the interface. All HTML is echoed.
     * @param robot_environment $re The associated robot_environment object for
     *     this interface
     */
    function generate($re)
    {
        global $title;
        
        // check if we have enough valid widgets
        /*if (!$streams = $re->get_widgets_by_name('MJPEG Stream')) {
            robot_environments::create_error_page(
                'No MJPEG streams found.',
                $re->get_user_account()
            );
        } else if (!$teleop = $re->get_widgets_by_name('Keyboard Teleop')) {
            robot_environments::create_error_page(
                'No Keyboard Teloperation settings found.',
                $re->get_user_account()
            );
        } else */ if (!$im = $re->get_widgets_by_name('Interactive Markers')) {
            robot_environments::create_error_page(
                'No Interactive Marker settings found.',
                $re->get_user_account()
            );
        }/* else if (!$nav = $re->get_widgets_by_name('2D Navigation')) {
            robot_environments::create_error_page(
                'No 2D Navaigation settings found.',
                $re->get_user_account()
            );
        } */ else if (!$re->authorized()) {
            robot_environments::create_error_page(
                'Invalid experiment for the current user.',
                $re->get_user_account()
            );
        } else {
            // lets create a string array of MJPEG streams
            $topics = '[';
            $labels = '[';
            foreach ($streams as $s) {
                $topics .= "'".$s['topic']."', ";
                $labels .= "'".$s['label']."', ";
            }
            $topics = substr($topics, 0, strlen($topics) - 2).']';
            $labels = substr($labels, 0, strlen($topics) - 2).']';

            // we will also need the map
            /*
            $widget = widgets::get_widget_by_table('maps');
            $map = widgets::get_widget_instance_by_widgetid_and_id(
                $widget['widgetid'], $nav[0]['mapid']
            );*/

            $collada = 'ColladaAnimationCompress/0.0.1/ColladaLoader2.min.js'?>
<!DOCTYPE html>
<html>
<head>
<?php $re->create_head() // grab the header information ?>
<title><?php echo $title?></title>
<script type="text/javascript"
  src="http://cdn.robotwebtools.org/threejs/r56/three.js">
</script>
<script type="text/javascript"
  src="http://cdn.robotwebtools.org/EventEmitter2/0.4.11/eventemitter2.js">
</script>
<script type="text/javascript"
  src="http://cdn.robotwebtools.org/<?php echo $collada?>">
</script>
<!-- http://cdn.robotwebtools.org/roslibjs/r5/ -->
<script type="text/javascript"
  src="/api/robot_environments/interfaces/robonaut_interface/roslib.js"></script>
<script type="text/javascript"
  src="http://cdn.robotwebtools.org/mjpegcanvasjs/r1/mjpegcanvas.min.js">
</script>
<script type="text/javascript"
  src="http://cdn.robotwebtools.org/keyboardteleopjs/r1/keyboardteleop.min.js">
</script>
<script type="text/javascript"
  src="/api/robot_environments/interfaces/robonaut_interface/ros3d.js">
  <!--src="http://cdn.robotwebtools.org/ros3djs/r6/ros3d.js"> -->
  <!--src="/api/robot_environments/interfaces/robonaut_interface/ros3d.js">-->
</script>
<script type="text/javascript"
  src="http://cdn.robotwebtools.org/EaselJS/0.6.0/easeljs.min.js">
</script>
<script type="text/javascript"
  src="http://cdn.robotwebtools.org/ros2djs/r2/ros2d.min.js">
</script>
<script type="text/javascript"
  src="http://cdn.robotwebtools.org/nav2djs/r1/nav2d.min.js">
</script>
<script type="text/javascript">
  //connect to ROS
  var ros = new ROSLIB.Ros({
    url : '<?php echo $re->rosbridge_url()?>'
  });

  ros.on('error', function() {
	writeToTerminal('Connection failed!');
  });

  /**
   * Write the given text to the terminal.
   *
   * @param text - the text to add
   */
  function writeToTerminal(text) {
    var div = $('#terminal');
    div.append('<strong> &gt; '+ text + '</strong><br />');
    div.animate({
      scrollTop : div.prop("scrollHeight") - div.height()
    }, 50);
  }

  /**
   * Load everything on start.
   */
  function start() {
  var R2pos = new ROSLIB.Topic({
      ros : ros,
      name : '/r2/r2_controller/left_arm/joint_command',
      messageType : 'sensor_msgs/JointState'
    });

  var hornsPos = new ROSLIB.Message({
      header : {frame_id : 'world'},
      name : ['/r2/right_arm/joint0', '/r2/right_arm/joint1', '/r2/right_arm/joint2', '/r2/right_arm/joint3', '/r2/right_arm/joint4', '/r2/right_arm/joint5', '/r2/right_arm/joint6', '/r2/right_arm/hand/thumb/joint0', '/r2/right_arm/hand/thumb/joint1', '/r2/right_arm/hand/thumb/joint2', '/r2/right_arm/hand/thumb/joint3', '/r2/right_arm/hand/index/joint0', '/r2/right_arm/hand/index/joint1', '/r2/right_arm/hand/index/joint2', '/r2/right_arm/hand/middle/joint0', '/r2/right_arm/hand/middle/joint1', '/r2/right_arm/hand/middle/joint2', '/r2/right_arm/hand/ring/joint0', '/r2/right_arm/hand/little/joint0'], 	
      position : [.23, -0.92, 2.53, -2.29, -2.19, .11, 0,1.22, 1.40, .28, -.39, -.28, 0, 0, 0, 1.57, 1.57, 1.57, 0]
    });

  var leftRP = new ROSLIB.Message({
      header : {frame_id : 'world'},
      name: ['/r2/left_arm/joint0', '/r2/left_arm/joint1', '/r2/left_arm/joint2', '/r2/left_arm/joint3', '/r2/left_arm/joint4', '/r2/left_arm/joint5', '/r2/left_arm/joint6', '/r2/left_arm/hand/thumb/joint0', '/r2/left_arm/hand/thumb/joint1', '/r2/left_arm/hand/thumb/joint2', '/r2/left_arm/hand/thumb/joint3', '/r2/left_arm/hand/index/joint0', '/r2/left_arm/hand/index/joint1', '/r2/left_arm/hand/index/joint2', '/r2/left_arm/hand/middle/joint0', '/r2/left_arm/hand/middle/joint1', '/r2/left_arm/hand/middle/joint2', '/r2/left_arm/hand/ring/joint0', '/r2/left_arm/hand/little/joint0'],
position: [0.8726646259971648, -1.3962634015954636, -1.8325957145940461, -2.443460952792061, 1.3962634015954636, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0]
    });

    var rightRP = new ROSLIB.Message({
      header : {frame_id : 'world'},
      name: ['/r2/right_arm/joint0', '/r2/right_arm/joint1', '/r2/right_arm/joint2', '/r2/right_arm/joint3', '/r2/right_arm/joint4', '/r2/right_arm/joint5', '/r2/right_arm/joint6', '/r2/right_arm/hand/thumb/joint0', '/r2/right_arm/hand/thumb/joint1', '/r2/right_arm/hand/thumb/joint2', '/r2/right_arm/hand/thumb/joint3', '/r2/right_arm/hand/index/joint0', '/r2/right_arm/hand/index/joint1', '/r2/right_arm/hand/index/joint2', '/r2/right_arm/hand/middle/joint0', '/r2/right_arm/hand/middle/joint1', '/r2/right_arm/hand/middle/joint2', '/r2/right_arm/hand/ring/joint0', '/r2/right_arm/hand/little/joint0'],
      position: [-0.8726646259971648, -1.3962634015954636, 1.8325957145940461, -2.443460952792061, -1.3962634015954636, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0]
    });

    var leftClose = new ROSLIB.Message({
      header : {frame_id : 'world'},
      name: ['/r2/left_arm/hand/thumb/joint0', '/r2/left_arm/hand/thumb/joint1', '/r2/left_arm/hand/thumb/joint2', '/r2/left_arm/hand/thumb/joint3', '/r2/left_arm/hand/index/joint0', '/r2/left_arm/hand/index/joint1', '/r2/left_arm/hand/index/joint2', '/r2/left_arm/hand/middle/joint0', '/r2/left_arm/hand/middle/joint1', '/r2/left_arm/hand/middle/joint2', '/r2/left_arm/hand/ring/joint0', '/r2/left_arm/hand/little/joint0'],
     position: [-1.7453292519943295, 0.0, 0.0, 0.0, 0.0, 1.7453292519943295, 1.2217304763960306, 0.0, 1.7453292519943295, 1.2217304763960306, 2.9670597283903604, 2.9670597283903604]
    });

    var leftOpen = new ROSLIB.Message({
      header : {frame_id : 'world'},
      name: ['/r2/left_arm/hand/thumb/joint0', '/r2/left_arm/hand/thumb/joint1', '/r2/left_arm/hand/thumb/joint2', '/r2/left_arm/hand/thumb/joint3', '/r2/left_arm/hand/index/joint0', '/r2/left_arm/hand/index/joint1', '/r2/left_arm/hand/index/joint2', '/r2/left_arm/hand/middle/joint0', '/r2/left_arm/hand/middle/joint1', '/r2/left_arm/hand/middle/joint2', '/r2/left_arm/hand/ring/joint0', '/r2/left_arm/hand/little/joint0'],
      position: [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0]
    });

    var headReady = new ROSLIB.Message({
      header : {frame_id : 'world'},
      name: ['/r2/neck/joint0', '/r2/neck/joint1', '/r2/neck/joint2'],
      position: [0.0, 0.0, -0.08726646259971647]
    });

    var rightClose = new ROSLIB.Message({
      header : {frame_id : 'world'},
      name: ['/r2/right_arm/hand/thumb/joint0', '/r2/right_arm/hand/thumb/joint1', '/r2/right_arm/hand/thumb/joint2', '/r2/right_arm/hand/thumb/joint3', '/r2/right_arm/hand/index/joint0', '/r2/right_arm/hand/index/joint1', '/r2/right_arm/hand/index/joint2', '/r2/right_arm/hand/middle/joint0', '/r2/right_arm/hand/middle/joint1', '/r2/right_arm/hand/middle/joint2', '/r2/right_arm/hand/ring/joint0', '/r2/right_arm/hand/little/joint0'],
position: [1.7453292519943295, 0.0, 0.0, 0.0, 0.0, 1.7453292519943295, 1.2217304763960306, 0.0, 1.7453292519943295, 1.2217304763960306, 2.6179938779914944, 2.6179938779914944]
    });

    var rightOpen = new ROSLIB.Message({
      header : {frame_id : 'world'},
      name: ['/r2/right_arm/hand/thumb/joint0', '/r2/right_arm/hand/thumb/joint1', '/r2/right_arm/hand/thumb/joint2', '/r2/right_arm/hand/thumb/joint3', '/r2/right_arm/hand/index/joint0', '/r2/right_arm/hand/index/joint1', '/r2/right_arm/hand/index/joint2', '/r2/right_arm/hand/middle/joint0', '/r2/right_arm/hand/middle/joint1', '/r2/right_arm/hand/middle/joint2', '/r2/right_arm/hand/ring/joint0', '/r2/right_arm/hand/little/joint0'],
position: [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0]

    });


  document.getElementById("horns").onclick=function(){
                R2pos.publish(hornsPos);
	};
  document.getElementById("leftRP").onclick=function(){
                R2pos.publish(leftRP);
        };
  document.getElementById("rightRP").onclick=function(){
                R2pos.publish(rightRP);
        };
  document.getElementById("leftClose").onclick=function(){
                R2pos.publish(leftClose);
        };
  document.getElementById("leftOpen").onclick=function(){
                R2pos.publish(leftOpen);
        };
  document.getElementById("headReady").onclick=function(){
                R2pos.publish(headReady);
        };
  document.getElementById("rightClose").onclick=function(){
                R2pos.publish(rightClose);
        };
  document.getElementById("rightOpen").onclick=function(){
                R2pos.publish(rightOpen);
        }; 


    // create MJPEG streams
    /*

    new MJPEGCANVAS.MultiStreamViewer({
      divID : 'video',
      host : '<?php echo $re->get_mjpeg()?>',
      port : '<?php echo $re->get_mjpegport()?>',
      width : 400,
      height : 300,
      topics : <?php echo $topics?>,
      labels : <?php echo $labels?>
    });*/

    // create the main viewer
    var viewer = new ROS3D.Viewer({
      divID : 'scene',
      width :  $(document).width(),
      height : $(document).height(),
      antialias : true
    });
    viewer.addObject(new ROS3D.Grid());

    // setup a client to listen to TFs
    var tfClient = new ROSLIB.TFClient({
      ros : ros,
      angularThres : 0.01,
      transThres : 0.01,
      rate : 10.0,
      fixedFrame : 'r2/robot_world' //'r2/robot_world'
    });
    
    /*
    new ROS3D.OccupancyGridClient({
      ros : ros,
      rootObject : viewer.scene,
      topic : '<?php echo $map['topic']?>',
      tfClient : tfClient
    });*/

    // setup the URDF client
    new ROS3D.UrdfClient({
      ros : ros,
      tfClient : tfClient,
      path : 'http://resources.robotwebtools.org/',
      rootObject : viewer.scene
    });

    // setup the marker clients
    // TODO : recopy this part from task_scheduler code
    <?php
    foreach ($im as $cur) {?>
      new ROS3D.InteractiveMarkerClient({
        ros : ros,
        tfClient : tfClient,
        topic : '<?php echo $cur['topic'] ?>',
        camera : viewer.camera,
        rootObject : viewer.selectableObjects,
        path : 'http://resources.robotwebtools.org/'
      });
    <?php 
    }
    ?>

    // move the overlays
    $('#terminal').css({top:($(document).height()-$('#terminal').height())+'px'});

    // fixes the menu in the floating camera feed
    $('body').bind('DOMSubtreeModified', function() {
    	$('body div:last-child').css('z-index', 750);
    });

    writeToTerminal('Interface initialization complete.');
  }
</script>
</head>
<body onload="start();">
  <button id="horns">Horns</button>
  <button id="headReady">Head Ready</button>
  <button id="leftRP">Left Ready Pos</button>
  <button id="rightRP">Right Ready Pos</button>
  <button id="leftClose">Left Close</button>
  <button id="leftOpen">Left Open</button>
  <button id="rightClose">Right Close</button>
  <button id="rightOpen">Right Open</button>
  <!-- <div class="mjpeg-widget" id="video"></div>-->
  <!-- <div class="nav-widget" id="nav"></div> -->
  <div id="terminal" class="terminal"></div>
  <div id="scene" class="scene"></div>
</body>
</html>
<?php
        }
    }
}
