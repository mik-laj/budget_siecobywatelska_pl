var color1 = d3.scale.ordinal()
    .range(['#D72626', '#F0F0F0', '#4C4C4C']);
var color2 = d3.scale.ordinal()
    .range(['white', '#4C4C4C', 'white']);

class Graph{

    constructor(el) {
        this.graph_el = d3.select(el)
        this.width = 750;
        this.height = 600;
        this.radius = Math.min(this.width, this.height) / 2;

        this.partition = d3.layout.partition()
                .size([2 * Math.PI, this.radius * this.radius])
                .value(d => 1);

        this.arc = d3.svg.arc()
                .startAngle(d => d.x)
                .endAngle(d => d.x + d.dx)
                .innerRadius(d => Math.sqrt(d.y))
                .outerRadius(d => Math.sqrt(d.y + d.dy));
    }

    initGraph() {
        this.svg = this.graph_el
            .append('svg')
            .attr('viewBox', `0 0 ${this.width} ${this.height}`);
        this.graph = this.svg.append("svg:g")
            .attr("id", "container")
            .attr("transform", `translate(${this.width / 2 } , ${this.height / 2} )`);
    }

    setRootCategoryData(root_category){
        function computeTextRotation(d) {
            var angle = ((d.x + d.dx / 2) - Math.PI / 2) / Math.PI * 180 - 90;
            if(angle > 90) angle -= 180;
            if(angle < -90) angle += 180;
            return angle;
        }
        let nodes = this.partition.nodes(root_category)
                .filter(d => d.depth > 0);

        let g = this.graph.selectAll('g')
                .data(nodes);
        var entering = g.enter().append('g');
        entering.append('path')
            .attr('d', this.arc)
            .attr('fill-rule', 'evenodd')
            .style('fill', d => d.color_bg)
            .style('opacity', 1)
            .on('click', d => this.listener(d))
            .each(function(d,i) {
                //Search pattern for everything between the start and the first capital L
                var firstArcSection = /(^.+?)L/;

                //Grab everything up to the first Line statement
                var newArc = firstArcSection.exec( d3.select(this).attr("d") )[1];
                //Replace all the commas so that IE can handle it
                newArc = newArc.replace(/,/g , " ");

                //If the end angle lies beyond a quarter of a circle (90 degrees or pi/2)
                //flip the end and start position
                if (d.endAngle > 90 * Math.PI/180) {
                    var startLoc 	= /M(.*?)A/,		//Everything between the capital M and first capital A
                        middleLoc 	= /A(.*?)0 0 1/,	//Everything between the capital A and 0 0 1
                        endLoc 		= /0 0 1 (.*?)$/;	//Everything between the 0 0 1 and the end of the string (denoted by $)
                    //Flip the direction of the arc by switching the start and end point (and sweep flag)
                    var newStart = endLoc.exec( newArc )[1];
                    var newEnd = startLoc.exec( newArc )[1];
                    var middleSec = middleLoc.exec( newArc )[1];

                    //Build up the new arc notation, set the sweep-flag to 0
                    newArc = "M" + newStart + "A" + middleSec + "0 0 0 " + newEnd;
                }//if

                //Create a new invisible arc that the text can flow along
                d3.select(this).append('path')
                    .attr('class', 'hiddenDonutArcs')
                    .attr('id', () => 'arc' + i)
                    .attr('d', newArc)
                    .style('fill', 'none');
            });

        // entering.append('text')
        //     .attr('fill', d=> d.color_text)
        //     .attr('dy', '.35em')
        //     .attr('font-size', '10')
        //     .attr('text-anchor', 'middle')
        //     .on('click', d => this.listener(d))
        //     .attr("transform", d => `translate(${this.arc.centroid(d)}) rotate(${computeTextRotation(d)})`)
        //     .text(function(d) { return d.title; })
        entering.append('text')
            .attr('fill', d=> d.color_text)
            .attr('font-size', '10')
            .attr('dy', '20px')
            .attr('dx', '5px')
            .append('textPath')
            .attr('startOffset', '50%')
            .style('text-anchor', 'middle')
            .attr('xlink:href', (d,i) => "#arc" + i)
            .text(d => d.title);

        // entering.append('text')
        //     .attr('fill', d=> d.color_text)
        //     .attr('dy', '.35em')
        //     .attr('font-size', '10')
        //     .attr('text-anchor', 'middle')
        //     .on('click', d => this.listener(d))
        //     .attr("transform", d => `translate(${this.arc.centroid(d)}) rotate(${computeTextRotation(d)})`)
        //     .text(function(d) { return d.title; })
    }

    setOnCategoryClickListener(fn){
        this.listener = fn;
    }
}

class Breadcrumbs{
    constructor(el) {
        // Breadcrumb dimensions: width, height, spacing, width of tip/tail.
        this.b = {
            h: 30, s: 3, t: 10, pl: 15, pr: 10
        };
        this.crumbs = [];
        this.breadcrumbs = d3.select(el);
        // d.w = this.getComputedTextLength();
    }

    init(){
        this.svg = this.breadcrumbs.append('svg')
            .attr('width', 750)
            .attr('height', 100);
    }
    setCurrentCategory(category){
        let crumbs = this._getAccesorsCategories(category).reverse();
        this.setCrumbs(crumbs);
    }

    setCrumbs(crumbs) {
        this.crumbs = crumbs;
        this._redraw();
    }

    _getAccesorsCategories(category){
        let current = category;
        let categories = [];
        while(current.parent != null) {
            categories.push(current);
            current = current.parent;
        }
        return categories;
    }

    _drawPoints(d, i){
        let points = [];
        let w = d.tw + this.b.pl + this.b.pr;
        if(i != 0){
            points.push(`${this.b.t},${this.b.h / 2}`);
        }
        points.push(`0,0`);
        points.push(`${w},0`);
        points.push(`${w + this.b.t},${this.b.h / 2}`);
        points.push(`${w},${this.b.h}`);
        points.push(`0,${this.b.h}`);
        return points.join(' ');
    }

    _redraw() {
        let g = this.svg.selectAll('g').data(this.crumbs);

        // ENTER
        var entering = g.enter().append('g').classed('crumbs', true);
        entering.append('polyline')
            .attr('fill', 'red');
        entering.append('text')
            .attr('dy', '.35em')
            .attr('y', this.b.h / 2)
            .attr('x', this.b.pl);
        // .attr('text-anchor', 'middle')



        // UPDATE
        g.select('text')
            .text(d => d.title)
            .attr('fill', d => d.color_text)
            .each(function(d){ d.tw = this.getComputedTextLength()});
        g.select('polyline')
            .attr('fill', d => d.color_bg)
            .attr('points', this._drawPoints.bind(this));

        g.each((d, i) => {
            if(i == 0){
                d.offset = 0;
                return;
            }
            let prev = g.data()[i - 1];
            d.offset = prev.offset + prev.tw + this.b.pl + this.b.pr + this.b.s;
        });
        g.attr('transform', (d, i) => `translate(${d.offset}, 0)`);

        // EXIT
        g.exit().remove();
    }

}

class Heading {
    constructor(el) {
        this.heading = d3.select(el);
    }

    setRootCategoryData(root_category) {
        this.heading.text(root_category.title);
    }
}

class List{
    constructor(el) {
        this.list = d3.select(el).append('ul').classed('contract-list', true);
        this.proto_row = document.createElement('li');
        this.proto_row.innerHTML = document.querySelector('#contract-template').innerHTML;
    }


    setContractsData(contracts){
        this.contracts = contracts;
        let li = this.list.selectAll('li').data(contracts);

        // ENTER
        let entering_div = li.enter().append(() => {
            return this.proto_row.cloneNode(true);
        });

        // UPDATE
        // category 	supplier 	title 	value
        var contract = li.select('div.contract');
        contract.select('.contract-category').text(d => d.category);
        contract.select('.contract-supplier').text(d => d.supplier);
        contract.select('.contract-title').text(d => d.title);
        contract.select('.contract-value').text(d => d.value);

        // EXIT
        li.exit().remove();
    }
}

class Pagination{
    constructor(el) {
        this.pagination = d3.select(el);
    }

    init(){
        this.list = this.pagination
            .append('ul')
            .classed('pagination', true);
    }
    setOnPageChangeListener(fn) {
        this.listener = fn;
    }

    setPageCount(page_count) {
        this.page_count = page_count;
        this.redraw();
    }

    setCurrentPage(page) {
        this.current_page = page;
        this.redraw();
    }
    redraw(){
        let li = this.list.selectAll('li')
            .data(d3.range(1, this.page_count + 1))
        li.enter()
            .append('li')
                .append('a')
                    .attr('href', '#')
                    .text(d => d)
                    .on('click', d => {
                        d3.event.preventDefault();
                        this.listener(d);
                    });
        li.classed('active', d => (this.current_page == d));
        li.exit().remove();
    }

}

class BudgetGraph{
    constructor(
        heading,
        breadcrumbs,
        graph,
        list,
        pagination
    ){
        this.heading = new Heading(heading);
        this.breadcrumbs = new Breadcrumbs(breadcrumbs);
        this.graph = new Graph(graph);
        this.list = new List(list);
        this.pagination = new Pagination(pagination);
    }

    init() {
        this.graph.initGraph();
        this.graph.setOnCategoryClickListener(d => this.setCategory(d));
        this.pagination.init();
        this.pagination.setOnPageChangeListener(p => this.setCategory(this.current_category, p));
        this.breadcrumbs.init();
    }

    setRootCategory(category_id) {
        d3.json('/budget/' + category_id + '/categories.json', data => {
            this.setRootCategoryData(data.category);
        });
    }

    setCategory(category, page = 1) {
        d3.json('/budget/' + category.id + '/contracts.json?page=' + page, data => {
            this.current_category = category;
            this.setContractsData(
                data.contracts,
                page,
                data.page_count);
            this.breadcrumbs.setCurrentCategory(category);
        });
    }

    setRootCategoryData(root_category) {
        this.root_category = root_category;
        this._initColors(root_category);
        this.graph.setRootCategoryData(root_category);
        this.heading.setRootCategoryData(root_category);
    }

    setContractsData(contracts, page, page_count) {
        this.list.setContractsData(contracts);
        this.pagination.setPageCount(page_count);
        this.pagination.setCurrentPage(page);
    }

    _initColors(root_category){
        var that = this;

        var i = 0;
        function visit(category){
            category.color_bg = color1(i);
            category.color_text = color2(i);

            category.children.forEach(visit);

            i++;
        }
        visit(root_category);
    }
}