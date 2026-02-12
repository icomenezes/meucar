    /**
    * o------------------------------------------------------------------------------o
    * | This file is part of the RGraph package - you can learn more at:             |
    * |                                                                              |
    * |                          http://www.rgraph.net                               |
    * |                                                                              |
    * | This package is licensed under the RGraph license. For all kinds of business |
    * | purposes there is a small one-time licensing fee to pay and for non          |
    * | commercial  purposes it is free to use. You can read the full license here:  |
    * |                                                                              |
    * |                      http://www.rgraph.net/LICENSE.txt                       |
    * o------------------------------------------------------------------------------o
    */
    
    if (typeof(RGraph) == 'undefined') RGraph = {};

    /**
    * The traditional radar chart constructor
    * 
    * @param string id   The ID of the canvas
    * @param array  data An array of data to represent
    */
    RGraph.Radar = function (id, data)
    {
        this.id                = id;
        this.canvas            = document.getElementById(id);
        this.context           = this.canvas.getContext('2d');
        this.canvas.__object__ = this;
        this.size              = null;// Set in the .Draw() method
        this.type              = 'radar';
        this.coords            = [];
        this.isRGraph          = true;
        this.data              = [];
        this.max               = 0;
        this.original_data     = [];

        for (var i=1; i<arguments.length; ++i) {
            this.original_data.push(RGraph.array_clone(arguments[i]));
            this.data.push(RGraph.array_clone(arguments[i]));
            this.max = Math.max(this.max, RGraph.array_max(arguments[i]));
        }

        /**
        * Compatibility with older browsers
        */
        RGraph.OldBrowserCompat(this.context);

        
        this.properties = {
            'chart.strokestyle':           'black',
            'chart.gutter.left':           25,
            'chart.gutter.right':          25,
            'chart.gutter.top':            25,
            'chart.gutter.bottom':         25,
            'chart.linewidth':             1,
            'chart.colors':                ['red', 'green', 'blue', 'pink', 'aqua','brown','orange','grey'],
            'chart.colors.alpha':          null,
            'chart.circle':                0,
            'chart.circle.fill':           'red',
            'chart.circle.stroke':         'black',
            'chart.labels':                [],
            'chart.labels.offsetx':        10,
            'chart.labels.offsety':        10,
            'chart.background.circles':    true,
            'chart.text.size':             10,
            'chart.text.font':             'Arial',
            'chart.text.color':            'black',
            'chart.title':                 '',
            'chart.title.background':      null,
            'chart.title.hpos':            null,
            'chart.title.vpos':            null,
            'chart.title.color':           'black',
            'chart.title.bold':             true,
            'chart.title.font':             null,
            'chart.linewidth':             1,
            
            'chart.key':                   null,
            'chart.key.background':        'white',
            'chart.key.shadow':            false,
            'chart.key.shadow.color':       '#666',
            'chart.key.shadow.blur':        3,
            'chart.key.shadow.offsetx':     2,
            'chart.key.shadow.offsety':     2,
            'chart.key.position':          'graph',
            'chart.key.halign':             'right',
            'chart.key.position.gutter.boxed': true,
            'chart.key.position.x':         null,
            'chart.key.position.y':         null,
            'chart.key.color.shape':        'square',
            'chart.key.rounded':            true,
            'chart.key.linewidth':          1,
            'chart.key.colors':             null,
            'chart.contextmenu':           null,
            'chart.annotatable':           false,
            'chart.annotate.color':        'black',
            'chart.zoom.factor':           1.5,
            'chart.zoom.fade.in':          true,
            'chart.zoom.fade.out':         true,
            'chart.zoom.hdir':             'right',
            'chart.zoom.vdir':             'down',
            'chart.zoom.frames':            25,
            'chart.zoom.delay':             16.666,
            'chart.zoom.shadow':           true,
            'chart.zoom.mode':             'canvas',
            'chart.zoom.thumbnail.width':  75,
            'chart.zoom.thumbnail.height': 75,
            'chart.zoom.thumbnail.fixed':   false,
            'chart.zoom.background':        true,
            'chart.zoom.action':            'zoom',
            'chart.tooltips.effect':        'fade',
            'chart.tooltips.css.class':      'RGraph_tooltip',
            'chart.tooltips.highlight':     true,
            'chart.highlight.stroke':       'gray',
            'chart.highlight.fill':         'white',
            'chart.resizable':              false,
            'chart.resize.handle.adjust':   [0,0],
            'chart.resize.handle.background': null,
            'chart.labels.axes':            '',
            'chart.ymax':                   null,
            'chart.accumulative':           false,
            'chart.radius':                 null,
            'chart.events.click':           null,
            'chart.events.mousemove':       null,
            'chart.scale.decimals':         0,
            'chart.scale.point':            '.',
            'chart.scale.thousand':         ',',
            'chart.units.pre':              '',
            'chart.units.post':             ''
        }
        
        // Must have at least 3 points
        for (var dataset=0; dataset<this.data.length; ++dataset) {
            if (this.data[dataset].length < 3) {
                alert('[RADAR] You must specify at least 3 data points');
                return;
            }
        }


        /**
        * Set the .getShape commonly named method
        */
        this.getShape = this.getPoint;
    }


    /**
    * A simple setter
    * 
    * @param string name  The name of the property to set
    * @param string value The value of the property
    */
    RGraph.Radar.prototype.Set = function (name, value)
    {
        this.properties[name] = value;

        /**
        * If the name is chart.color, set chart.colors too
        */
        if (name == 'chart.color') {
            this.properties['chart.colors'] = [value];
        }
    }


    /**
    * A simple hetter
    * 
    * @param string name  The name of the property to get
    */
    RGraph.Radar.prototype.Get = function (name)
    {
        return this.properties[name];
    }


    /**
    * The draw method which does all the brunt of the work
    */
    RGraph.Radar.prototype.Draw = function ()
    {
        /**
        * Fire the onbeforedraw event
        */
        RGraph.FireCustomEvent(this, 'onbeforedraw');

        /**
        * Clear all of this canvases event handlers (the ones installed by RGraph)
        */
        RGraph.ClearEventListeners(this.id);
        
        /**
        * Reset the data to the original_data
        */
        this.data = RGraph.array_clone(this.original_data);
        
        // Loop thru the data array if chart.accumulative is enable checking to see if all the
        // datasets have the same number of elements.
        if (this.Get('chart.accumulative')) {
            for (var i=0; i<this.data.length; ++i) {
                if (this.data[i].length != this.data[0].length) {
                    alert('[RADAR] Error! When the radar has chart.accumulative set to true all the datasets must have the same number of elements');
                }
            }
        }
        
        /**
        * This is new in May 2011 and facilitates indiviual gutter settings,
        * eg chart.gutter.left
        */
        this.gutterLeft   = this.Get('chart.gutter.left');
        this.gutterRight  = this.Get('chart.gutter.right');
        this.gutterTop    = this.Get('chart.gutter.top');
        this.gutterBottom = this.Get('chart.gutter.bottom');

        this.centerx  = ((this.canvas.width - this.gutterLeft - this.gutterRight) / 2) + this.gutterLeft;
        this.centery  = ((this.canvas.height - this.gutterTop - this.gutterBottom) / 2) + this.gutterTop;
        this.size     = Math.min(this.canvas.width - this.gutterLeft - this.gutterRight, this.canvas.height - this.gutterTop - this.gutterBottom);
        
        if (typeof(this.Get('chart.radius')) == 'number') {
            this.size = 2 * this.Get('chart.radius');
        }

        // Work out the maximum value and the sum
        if (!this.Get('chart.ymax')) {

            // this.max is calculated in the constructor

            // Work out this.max again if the chart is (now) set to be accumulative
            if (this.Get('chart.accumulative')) {
                
                var accumulation = [];
                var len = this.original_data[0].length

                for (var i=1; i<this.original_data.length; ++i) {
                    if (this.original_data[i].length != len) {
                        alert('[RADAR] Error! Stacked Radar chart datasets must all be the same size!');
                    }
                    
                    for (var j=0; j<this.original_data[i].length; ++j) {
                        this.data[i][j] += this.data[i - 1][j];
                        this.max = Math.max(this.max, this.data[i][j]);
                    }
                }
            }

            this.scale = RGraph.getScale(this.max, this);
            this.max = this.scale[4];
        
        } else {
            var ymax = this.Get('chart.ymax');

            this.scale = [
                          ymax * 0.2,
                          ymax * 0.4,
                          ymax * 0.6,
                          ymax * 0.8,
                          ymax * 1
                         ];
            this.max = this.scale[4];
        }

        this.DrawBackground();
        this.DrawAxes();
        this.DrawCircle();
        this.DrawAxisLabels();
 
        /**
        * Install the clickand mousemove event listeners
        */
        RGraph.InstallUserClickListener(this, this.Get('chart.events.click'));
        RGraph.InstallUserMousemoveListener(this, this.Get('chart.events.mousemove'));
        
        this.DrawChart();
        this.DrawLabels();
        
        // Draw the title
        if (this.Get('chart.title')) {
            RGraph.DrawTitle(this.canvas, this.Get('chart.title'), this.gutterTop, null, this.Get('chart.title.size') ? this.Get('chart.title.size') : null)
        }

        // Draw the key if necessary
        // obj, key, colors
        if (this.Get('chart.key')) {
            RGraph.DrawKey(this, this.Get('chart.key'), this.Get('chart.colors'));
        }

        /**
        * Show the context menu
        */
        if (this.Get('chart.contextmenu')) {
            RGraph.ShowContext(this);
        }

        /**
        * If the canvas is annotatable, do install the event handlers
        */
        if (this.Get('chart.annotatable')) {
            RGraph.Annotate(this);
        }

        /**
        * This bit shows the mini zoom window if requested
        */
        if (this.Get('chart.zoom.mode') == 'thumbnail' || this.Get('chart.zoom.mode') == 'area') {
            RGraph.ShowZoomWindow(this);
        }

        
        /**
        * This function enables resizing
        */
        if (this.Get('chart.resizable')) {
            RGraph.AllowResizing(this);
        }


        /**
        * This function enables adjusting
        */
        if (this.Get('chart.adjustable')) {
            RGraph.AllowAdjusting(this);
        }
        
        /**
        * Fire the RGraph ondraw event
        */
        RGraph.FireCustomEvent(this, 'ondraw');
    }


    /**
    * Draws the background circles
    */
    RGraph.Radar.prototype.DrawBackground = function ()
    {
        var color = '#ddd';

        /**
        * Draws the background circles
        */
        if (this.Get('chart.background.circles')) {

           this.context.strokeStyle = color;
           this.context.beginPath();

           for (var r=5; r<(this.size / 2); r+=15) {

                this.context.moveTo(this.centerx, this.centery);
                this.context.arc(this.centerx, this.centery,r, 0, 6.28, 0);
            }
            
            this.context.stroke();
        
        
            /**
            * Draw diagonals
            */
            this.context.strokeStyle = color;
            for (var i=0; i<360; i+=15) {
                this.context.beginPath();
                this.context.arc(this.centerx, this.centery, this.size / 2, (i / 360) * (2 * Math.PI), ((i+0.01) / 360) * (2 * Math.PI), 0); // The 0.01 avoids a bug in Chrome 6
                this.context.lineTo(this.centerx, this.centery);
                this.context.stroke();
            }
        }
    }


    /**
    * Draws the axes
    */
    RGraph.Radar.prototype.DrawAxes = function ()
    {
        this.context.strokeStyle = 'black';

        var halfsize = this.size / 2;

        this.context.beginPath();

        /**
        * The Y axis
        */
        this.context.moveTo(AA(this, this.centerx), this.centery + halfsize);
        this.context.lineTo(AA(this, this.centerx), this.centery - halfsize);
        

        // Draw the bits at either end of the Y axis
        this.context.moveTo(this.centerx - 5, AA(this, this.centery + halfsize));
        this.context.lineTo(this.centerx + 5, AA(this, this.centery + halfsize));
        this.context.moveTo(this.centerx - 5, AA(this, this.centery - halfsize));
        this.context.lineTo(this.centerx + 5, AA(this, this.centery - halfsize));
        
        // Draw Y axis tick marks
        for (var y=(this.centery - halfsize); y<(this.centery + halfsize); y+=15) {
            this.context.moveTo(this.centerx - 3, AA(this, y));
            this.context.lineTo(this.centerx + 3, AA(this, y));
        }

        /**
        * The X axis
        */
        this.context.moveTo(this.centerx - halfsize, AA(this, this.centery));
        this.context.lineTo(this.centerx + halfsize, AA(this, this.centery));

        // Draw the bits at the end of the X axis
        this.context.moveTo(AA(this, this.centerx - halfsize), this.centery - 5);
        this.context.lineTo(AA(this, this.centerx - halfsize), this.centery + 5);
        this.context.moveTo(AA(this, this.centerx + halfsize), this.centery - 5);
        this.context.lineTo(AA(this, this.centerx + halfsize), this.centery + 5);

        // Draw X axis tick marks
        for (var x=(this.centerx - halfsize); x<(this.centerx + halfsize); x+=15) {
            this.context.moveTo(AA(this, x), this.centery - 3);
            this.context.lineTo(AA(this, x), this.centery + 3);
        }

        /**
        * Finally draw it to the canvas
        */
        this.context.stroke();
    }


    /**
    * The function which actually draws the radar chart
    */
    RGraph.Radar.prototype.DrawChart = function ()
    {
        var alpha = this.Get('chart.colors.alpha');

        if (typeof(alpha) == 'number') {
            var oldAlpha = this.context.globalAlpha;
            this.context.globalAlpha = alpha;
        }
        
        var numDatasets = this.data.length;

        for (var dataset=0; dataset<this.data.length; ++dataset) {
// =============================================================================================== //
            this.context.beginPath();
            
                this.coords[dataset] = [];
    
                for (var i=0; i<this.data[dataset].length; ++i) {
                    this.coords[dataset][i] = this.GetCoordinates(dataset, i);
                }
    
                /**
                * Now go through the coords and draw the chart itself
                */
                this.context.strokeStyle = this.Get('chart.strokestyle');
                this.context.fillStyle   = this.Get('chart.colors')[dataset];
                this.context.lineWidth   = this.Get('chart.linewidth');

                for (i=0; i<this.coords[dataset].length; ++i) {
                    if (i == 0) {
                        this.context.moveTo(this.coords[dataset][i][0], this.coords[dataset][i][1]);
                    } else {
                        this.context.lineTo(this.coords[dataset][i][0], this.coords[dataset][i][1]);
                    }
                }
                
                // If on the second or greater dataset, backtrack
                if (this.Get('chart.accumulative') && dataset > 0) {

                    this.context.lineTo(this.coords[dataset][0][0], this.coords[dataset][0][1]);
                    this.context.lineTo(this.coords[dataset][0][0], this.coords[dataset - 1][0][1]);

                    for (var i=this.coords[dataset].length - 1; i>=0; --i) {
                        this.context.lineTo(this.coords[dataset - 1][i][0], this.coords[dataset - 1][i][1]);
                    }
                }
            
            this.context.closePath();
    
            this.context.stroke();
            this.context.fill();
// =============================================================================================== //
        }
        
        // Reset the globalAlpha
        if (typeof(alpha) == 'number') {
            this.context.globalAlpha = oldAlpha;
        }

        /**
        * Can now handletooltips
        */
        if (this.Get('chart.tooltips')) {
            
            RGraph.Register(this);
            
            RGraph.PreLoadTooltipImages(this);
            
            var canvas_onmousemove_func = function (e)
            {
                e = RGraph.FixEventObject(e);
                
                var canvas      = e.target;
                var obj         = canvas.__object__;
                var overHotspot = false;
                var point = obj.getPoint(e);
    
    
                if (point) {
    
                    var dataset = point[3];
                    var idx     = point[4];
    
                    if (   !RGraph.Registry.Get('chart.tooltip')
                        || (RGraph.Registry.Get('chart.tooltip').__index__ != idx && RGraph.Registry.Get('chart.tooltip').__dataset__ != dataset)
                        || (RGraph.Registry.Get('chart.tooltip').__index__ != idx && RGraph.Registry.Get('chart.tooltip').__dataset__ == dataset)
                       ) {
    
                        /**
                        * Get the tooltip text
                        */
                        var text = RGraph.parseTooltipText(obj.Get('chart.tooltips'), idx);
    
                        if (typeof(text) == 'string' && text.length) {
                   
                            overHotspot = true;
                            obj.canvas.style.cursor = 'pointer';
    
                            RGraph.Clear(obj.canvas);
                            obj.Draw();
                            
                            if (obj.Get('chart.tooltips.highlight')) {
                                obj.context.beginPath();
                                obj.context.strokeStyle = obj.Get('chart.highlight.stroke');