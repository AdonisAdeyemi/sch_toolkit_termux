/* /js/picker.js
   jQuery full picker (mobile-first)
   - 3 tabs: filter_pane_id, available_pane_id, picked_pane_id
   - API: GET /api/questions?subject=&arm=&exam_body=&year=&topic=&type=&page=&searchTerm=
   - localStorage key: picker_selections
   - picked -> stored as array but send to server as {compilation_id, question_id, item_order}
   - Buttons at bottom of each question card
*/

(function($){
  'use strict';

alert("Welcome to q-picker.");


  // ====== CONFIG ======
  let appName = window.appName;
  const API_Q = `/${appName}/api/questions/query_and_search`;
  const SAVE_ENDPOINT = `/${appName}/api/compilation/save_items`; // adjust if needed
 const GET_ENDPOINT = `/${appName}/api/compilation/get_items`;
 const Q_DROPDOWN_ENDPOINT = `/${appName}/api/questions/dropdown_list`;
  const LS_PREFIX = 'comp_';
  const PER_PAGE = 50;

  // ====== STATE ======
  const state = {
    filters: {}, // subject, arm, exam_body, year, topic, type, searchTerm
    available: [], // current page items
    page: 1,
    total_pages: 1,
    compilation_id : null,
    picked: [], // { compilation_id, question_id, item_order, question_raw }
    isServerUpToDate : false,
    max_pick : 50
  };

  // ====== UTIL ======
  function el(id){ return $('#' + id); }
  function nowISO(){ return new Date().toISOString(); }
  function short(s, n=400){ if(!s) return ''; return s.length>n? s.slice(0,n-1)+'…' : s; }
  function safeParseOptions(raw){
    // raw may be null, an already-serialized string like "{\"A\":\"F=ma\"}", or an object
    if (!raw) return null;
    // if it's an object already:
    if (typeof raw === 'object') return raw;
    try {
      return JSON.parse(raw);
    } catch (e) {
      // maybe it's a stringified JSON within a string (rare)
      try { return JSON.parse(String(raw).replace(/\\\"/g,'"')); } catch(e2){ return null; }
    }
  }
  function saveLS(){
    try {
      const minimal = state.picked.map((p, idx) => ({
        compilation_id: p.compilation_id || null,
        question_id: p.question_id,
        item_order: idx,
        question_raw: p.question_raw || null
      }));
      
     let ls_key = LS_PREFIX + state.compilation_id;
      
      localStorage.setItem(ls_key, JSON.stringify({saved_at: nowISO(), picks: minimal}));
      // show small message
      $('#availMeta').text('Local saved');
    } catch(e){ console.warn('LS save failed', e); }
  }
  function loadLS(){
  if(!state.isServerUpToDate){
    try {
    let ls_key = LS_PREFIX + state.compilation_id;
    
      const raw = localStorage.getItem(ls_key);
      if (!raw) return false;
      const parsed = JSON.parse(raw);
      if (!parsed || !Array.isArray(parsed.picks)) return false;
      state.picked = parsed.picks.map((p, idx) => ({
        compilation_id: p.compilation_id || null,
        question_id: p.question_id,
        item_order: p.item_order != null ? p.item_order : idx,
        question_raw: p.question_raw || null
      }));
      
      //load the compilation_id  
      if(state.picked.length > 0)
      {
      state.compilation_id = state.picked[0].compilation_id  ;
      }
      
      return true;
    } catch(e){ console.warn('LS load failed', e); return false; }
    }
  }

  // ====== RENDER HELPERS ======
  function renderAvailable(){
    const $container = el('available_section_id');
    $container.empty();
    if (!state.available || state.available.length === 0){
      $container.append('<div class="text-muted">No questions found.</div>');
      el('availableCount').text('Available (0)');
      el('pageInfo').text('');
      return;
    }
      
    state.available.forEach(q => {
      const isPicked = state.picked.some(p => String(p.question_id) === String(q.id));
      
let highlightClass = isPicked ? 'picked-highlight' : ''; //for highlighting picked items
      
      // prepare options display for MCQ
      let optionsHtml = '';
      if (q.type === 'mcq' && q.options){
        const opts = safeParseOptions(q.options);
        if (opts && typeof opts === 'object'){
          const parts = Object.keys(opts).map(k => `<span class="opt-pill">(${escapeHtml(k)}) ${escapeHtml(opts[k])}</span>`);
          optionsHtml = `<div class="options-line">${parts.join('')}</div>`;
        }
      }

let imgHtml = getItemHtml(q);

 
 // correct option display (optional small tag)
      let correctHtml = '';
      if (q.type === 'mcq' && q.answer){
        correctHtml = `<div class="small text-success mt-1">Correct: (${escapeHtml(q.answer)})</div>`;
      }
      
    let serialNumberSpan = ` #<span>${q.q_label}</span>. `;
          
      
      
      
      const card = $(`
        <div class="question-card ${highlightClass}" data-qid="${escapeHtml(q.id)}">
          <div class="question-text"> ${serialNumberSpan} ${escapeHtml(short(q.question_text || q.question || ''))}</div>
          ${optionsHtml}
          ${imgHtml}
          ${correctHtml}
          <div class="question-meta small text-muted mt-2">${escapeHtml([q.subject || '', q.topic || '', q.year || ''].filter(Boolean).join(' • '))}</div>
          <div class="card-footer-actions">
            <div class="small text-muted">${escapeHtml(q.type || '')} • Q#${escapeHtml(q.q_label || '')}</div>
            <div>
              <button class="btn btn-sm ${isPicked ? 'btn-outline-danger unpick-btn' : 'btn-primary pick-btn'}" data-qid="${escapeHtml(q.id)}">
                ${isPicked ? 'Remove' : 'Pick'}
              </button>
            </div>
          </div>
        </div>
      `);
      $container.append(card);
    });

   //fill info elements
    el('availableCount').text('Available ('+ state.available.length +')');
    el('pageInfo').text('Page ' + state.page + ' / ' + state.total_pages);
    
    
   

  }
    
    /************/
    
    //helper function  
  function getItemHtml (item)
  {
     // IMAGE display
var baseUrl = "/app_" + appName + "/public/uploads/images/";
var defaultImage = "/app_" + appName + "/public/uploads/images/default_img.jpg";
let imgHtml ="";

// 2. The Logic
if (item.has_image) {
    
    var imageUrlData = item.image_url //refactor - remove item.image_filename from db;
   // var imgUrl;

    // Logic: Determine initial URL
    if (!imageUrlData) { 
     //   imgUrl = defaultImage;
    } else {
      //  imgUrl = baseUrl + rawName;
    
    
    
    let images = [];
        

    // 1. Try to parse the JSON if it's a string
    try {
        images = typeof imageUrlData === 'string' ? JSON.parse(imageUrlData) : imageUrlData;
    } catch (e) {
        console.error("Invalid JSON for images:", e);
        return; // Stop if the data is corrupted
    }
    
     // 3. Check if we have an array with items
    if (Array.isArray(images) && images.length > 0) {
        $.each(images, function(index, imgName) {
            // add the image to html

  var fullImgPath = baseUrl + imgName ;
       
imgHtml += `
    <div>
        <img 
            src="${fullImgPath}" 
            class="p-2 img-fluid mx-auto d-block"
            style="width:90%; height:auto;" 
            onerror="this.onerror=null; this.src='${defaultImage}';" 
            alt="Item Image"
        >
    </div>
`; 


            
        });
    }
     
    
    

   // displayImages(imageUrlData, containerId);
    
    

 };
   }
 return imgHtml;
    
      }
    
    
    /******
    ***
    ***
    *******/
    
    
   /*********/ 
    /***********/
    /*********/
    
  function renderPicked(){
    const $picked = el('picked_section_id');
    $picked.empty();
    if (!state.picked || state.picked.length === 0){
      $picked.append('<div class="text-muted">No picked questions yet.</div>');
      $('#pickedCount').text(0);
      return;
    }
      
    //count number of type=info (not mcq,theory)
  let pickedLength_noInfo = getCountOfPickedQs_noInfo();
  let serialNo_noInfo = 0; //counter of real questions. mcq,theory
    
      
      
      
      
    // most recent on top as requested: show reversed copy without changing order in state
    const reversed = [].concat(state.picked).reverse();
    reversed.forEach((p, idx) => {
      const q = p.question_raw || {};
      let optionsHtml = '';
      if (q.type === 'mcq' && q.options){
        const opts = safeParseOptions(q.options);
        if (opts && typeof opts === 'object'){
          const parts = Object.keys(opts).map(k => `<span class="opt-pill">(${escapeHtml(k)}) ${escapeHtml(opts[k])}</span>`);
          optionsHtml = `<div class="options-line">${parts.join('')}</div>`;
        }
      }
      const removeBtn = 
      `<div class="text-end">
      <button class="btn btn-sm btn-outline-danger remove-picked "  data-qid="${escapeHtml(p.question_id)}">Remove</button>
      
    </div>`;
        
        
        
        
      /*
   let serialNo = 0;
    //add serial number
      let serialNumberSpan = "";
     
    if(q.type!= 'info') 
        {
        serialNumberSpan = ` #<span class="q-index"></span>. `;
        }
      
      
      */
       // console.log("idx",idx);
   
    let q_type = p.question_raw.type;   
    
    let reversedS_N = "";
    
  let serialNumberSpan = "";
 if (q_type != 'info') //ie.either mcq or thoery, skip counter
     {
        reversedS_N = pickedLength_noInfo - serialNo_noInfo ;
         
 serialNumberSpan  = ` #<span>${reversedS_N}</span>. `;
         serialNo_noInfo++;
      }
let imgHtml = getItemHtml(q);  
 let q_short_text = short((q.question_text || q.question || ''),40);
           
      const card = $(`
        <div class="picked-item picked-highlight mb-2 p-2 ">
          <div class="d-flex justify-content-between">
            <div><strong> ${serialNumberSpan} ${escapeHtml(q_short_text)}</strong>
              <div class="small text-muted">${escapeHtml([q.subject || '', q.year || '', q.type || ''].filter(Boolean).join(' • '))}</div>
            </div>
                
          </div>
          ${optionsHtml}
           ${imgHtml}
          ${removeBtn} 
        </div>
         
      `);
      $picked.append(card);
    });
   console.log("noinfo Qs" ,getCountOfPickedQs_noInfo()); 
     // $('#pickedCounter').text(getCountOfPickedQs_noInfo());
      
    updateProgressBar();
      
/*
//add serial number
$('#picked_pane_id .picked-item').each(function(i){
let pickedLength = state.picked.length ;
let reversePosition = pickedLength - i;
    $(this).find('.q-index').text(reversePosition);
});
*/

  }
  
    /***** helper of renderPicked()*****/
 function getCountOfTypeInfo(questionsArr)
{
 let count = 0;
 $(questionsArr).each(function(i){
    let q_type = questionsArr[i].question_raw.type;
  if(q_type == 'info') count++;
        })
    return count;
}
    
    /********/
    
   function getCountOfPickedQs_noInfo()
    {
        let  infoCount = getCountOfTypeInfo (state.picked);
  let pickedLength = state.picked.length ;
  let pickedLength_noInfo = pickedLength - infoCount;
    return pickedLength_noInfo;
     }
    
    

  // ====== CORE LOGIC ======
  function fetchPage(page = 1){
    state.page = page || 1;
    const params = {
      page: state.page,
      per_page: PER_PAGE
    };
    // attach filters
    ['subject','arm','exam_body','year','topic','type','searchTerm'].forEach(k => {
      if (state.filters[k]) params[k] = state.filters[k];
    });
    $('#availMeta').text('Loading...');
      $('#feedback').text('Loading...');
    $.ajax({
      url: API_Q,
      method: 'GET',
      data: params,
      dataType: 'json'
    }).done(function(json){
       console.log('API response', json); // uncomment to debug
       
      if (!json || json.status !== 'success'){
        $('#availMeta').text('Server error');
         $('#feedback').text('Server error');
        state.available = [];
        renderAvailable();
        return;
      }
      
      
      // resolve questions array field name
      const questions = Array.isArray(json.questions) ? json.questions : (Array.isArray(json.data) ? json.data : []);
      state.available = questions;
      const pagination = json.pagination || json.paging || {page: state.page, total_pages: 1};
      state.page = pagination.page || state.page;
      state.total_pages = pagination.total_pages || pagination.last_page || Math.max(1, Math.ceil((json.total || questions.length)/PER_PAGE));
      renderAvailable();
      $('#availMeta').text('');
       $('#feedback').text('Your changes havn\'t been saved yet.');
      
    }).fail(function(xhr, status, err){
    
    console.log("Response Text:", xhr.responseText); 
    
      $('#availMeta').text('Network error');
      state.available = [];
      renderAvailable();
    });
  }

  function addPickFromAvailable(qid){
      let picksCount = getCountOfPickedQs_noInfo();
      
      if(picksCount < state.max_pick)
      {
    // find q in available (search current page)
    const q = state.available.find(x => String(x.id) === String(qid));
    if (!q) return;
    if (state.picked.some(p => String(p.question_id) === String(q.id))) return;
    state.picked.push({
      compilation_id: state.compilation_id,
      question_id: q.id,
      item_order: state.picked.length,
      question_raw: q
    });
    
  console.log( "add pick : state.picked:", state.picked)
    
    updateProgressBar();
    // persist but do NOT switch tab
    saveLS();
    renderAvailable();
    renderPicked();
    }
    else {
        alert ('Max picks reached.');
        }
  }
    
    
  function removePickById(qid){
    state.picked = state.picked.filter(p => String(p.question_id) !== String(qid));
    // renumber orders
    state.picked.forEach((p,i) => p.item_order = i);
    updateProgressBar();
    saveLS();
    renderAvailable();
    renderPicked();
  }

  function shufflePicked(){
    for (let i = state.picked.length -1; i > 0; i--){
      const j = Math.floor(Math.random() * (i + 1));
      [state.picked[i], state.picked[j]] = [state.picked[j], state.picked[i]];
    }
    state.picked.forEach((p,i) => p.item_order = i);
    saveLS();
    renderPicked();
  }

  // Save to server in required shape
  function saveSelectionsToServer(){
  console.log ("xxxxx - in selections server");
  
  const compilationId = state.picked.length > 0
        ? state.compilation_id
        : null;
        
  const items = state.picked.map((p, idx) => ({
        question_id: p.question_id,
        item_order: idx
    }));
    
    console.log("items", items);

  
    // show quick feedback
    $('#availMeta').text('Saving...');
    $.ajax({
      url: SAVE_ENDPOINT,
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(
      {
      compilation_id: compilationId,
      items: items
    }
      ),
      dataType: 'json'
    }).done(function(resp){
      $('#availMeta').text('Saved to server');
         $('#feedback').text('Saved to server');
      alert('Saved selections to server.');
      state.isServerUpToDate = true;
    }).fail(function(xhr, status, err){

    console.log("Response Text:", xhr.responseText); 
    
      $('#availMeta').text('Save failed');
      $('#feedback').text('Save failed');
      alert('Failed to save selections to server.');
    });
  }

  // ====== UI WIRING ======
  function wireUI(){
    // filters: populate from query string if present
    (function readFiltersFromQuery(){
      const params = new URLSearchParams(window.location.search);
      ['subject','arm','exam_body','year','topic','type','searchTerm'].forEach(k=>{
        if (params.has(k)){
          state.filters[k] = params.get(k);
          // reflect in filter form inputs
          const map = {
            subject: 'f_subject',
            arm: 'f_arm',
            exam_body: 'f_exam_body',
            year: 'f_year',
            topic: 'f_topic',
            type: 'f_type',
            searchTerm: 'f_searchTerm'
          };
          const id = map[k];
          if (id) $('#' + id).val(params.get(k));
        }
      });
    })();

    // Apply filters button updates Available list only
    $('#applyFiltersBtn').on('click', function(e){
    alert ('Apply heard : clicked');
    /***** validator - preventer of empty subject field ******/
    e.preventDefault();
    // Get value 
    const subject = $("#f_subject").val().trim();

    // Hide error first
    $("#subject_error").hide();

    // Validate
    if (subject === "") {
        $("#subject_error").show();
        return;
    }
    
   
      // read form inputs
      state.filters.subject = $('#f_subject').val().trim() || undefined;
      state.filters.arm = $('#f_arm').val().trim() || undefined;
      state.filters.exam_body = $('#f_exam_body').val().trim() || undefined;
      state.filters.year = $('#f_year').val().trim() || undefined;
      state.filters.topic = $('#f_topic').val().trim() || undefined;
      state.filters.type = $('#f_type').val() || undefined;
      state.filters.searchTerm = $('#f_searchTerm').val().trim() || undefined;
      state.page = 1;
      fetchPage(state.page);
      // Do not touch state.picked
   
    // 2️⃣ switch to Available Questions tab
    var tab = new bootstrap.Tab($('#pickerTabs button[data-bs-target="#available_pane_id"]')[0]);
    tab.show();
   
    });
    
    $('#clearFiltersBtn').on('click', function(){
      $('#filtersForm')[0].reset();
      state.filters = {};
      state.page = 1;
      fetchPage(state.page);
    });

    // pagination
    $('#prev_btn_id').on('click', function(){ if (state.page>1) { state.page--; fetchPage(state.page); } });
    $('#next_btn_id').on('click', function(){ if (state.page < state.total_pages) { state.page++; fetchPage(state.page); } });

    // delegate pick/unpick buttons in available
    $('#available_section_id').on('click', '.pick-btn', function(){
      const qid = $(this).data('qid');
      addPickFromAvailable(qid);
      
       $('#feedback').text('Your changes havn\'t been saved yet.');
       state.isServerUpToDate = false; 
    });
    $('#available_section_id').on('click', '.unpick-btn', function(){
      const qid = $(this).data('qid');
      removePickById(qid);
      state.isServerUpToDate = false;
    });

    // delegate remove in picked tab
    $('#picked_section_id').on('click', '.remove-picked', function(){
      const qid = $(this).data('qid');
      removePickById(qid);
    });

    // shuffle
    $('#shuffle_btn_id').on('click', function(){ shufflePicked(); });

    // clear picked from header
    $('#clearPickedBtn').on('click', function(){ if (!confirm('Clear all picks?')) return; 
    state.picked = []; 
    updateProgressBar();
    saveLS(); 
    renderPicked();
     renderAvailable(); 
     
     });
    
    

    // save to server
    $('#saveSelectionsBtn').on('click', function(){ saveSelectionsToServer(); });
  }

  // Small helper to escape html
  function escapeHtml(s){
    if (s == null) return '';
    return String(s).replace(/[&<>"'`]/g, function (c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#x60;'}[c]; });
  }

  // ====== BOOT ======
  $(async function(){
    state.compilation_id = window.GET.id ;
    console.log("BOOT -- state.compilation_id:", state.compilation_id);
    if (!state.compilation_id )
    {
     return;
    }
    
    const lsKey = LS_PREFIX + state.compilation_id ;
    const ls = localStorage.getItem(lsKey);
let compilation_items = await fetchCompilationItems (state.compilation_id );
   
   console.log ("compilation items", compilation_items)

    // Case 1: Server is NEW + LS has data → unsaved progress
    if (compilation_items.length === 0 && ls) {
        
   let isLoadLS_success = loadLS();
     if( !isLoadLS_success )
      {
      console.log("Problem with loading from LocalStorage");
      return;
      }
      
      operationsAfterStateUpdate_DuringBooting();
      
        state.isServerUpToDate = false;
      $('#feedback').text('Loaded unsaved progress from LocalStorage');
        console.log("Loaded unsaved progress from LocalStorage");
        
        return;
    }

    // Case 2: Server's compilation_items has data → ignore LS 
    
    updateStateWithCompilationInfo(state.compilation_id, compilation_items );
operationsAfterStateUpdate_DuringBooting();
    state.isServerUpToDate = true;
    
    console.log("Loaded saved progress from server");
  
  /** populate filter fields */
  loadSubjects();
  
  });
  

/***xxxxxxxxxxxxxxxxxxxx***/

  //fetch compilation items  and update state 
  function fetchCompilationItems(compilation_id){
return new Promise((resolve, reject) => {
$.ajax({
    url: GET_ENDPOINT,
    method: "GET",
    data: { compilation_id : compilation_id }
})
.done(function(response) {
    console.log("Success:", response);
    
    resolve( response.compilation_items);
    
})
.fail(function(xhr, status, error) {
    console.log("Failed:", status, error);
    console.log("Server Response:", xhr.responseText);
});
  
  })
  }
  
  //operationsAfterStateUpdate_DuringBooting 
  function operationsAfterStateUpdate_DuringBooting()
  {
  console.log("in booting post ops");
    updateProgressBar();
    renderPicked();
    wireUI();

    // initial fetch for available tab (with any filters from query) BUT check if params is valid
     const params = new URLSearchParams(window.location.search);
        if (params.has('subject')){
  fetchPage(state.page);      
}

    // autosave every 8s
    setInterval(saveLS, 8000);
    // save on unload
    $(window).on('beforeunload', saveLS);
    //disable buttons for view-only
   disableElemsForViewOnly();
  }
  
  /*
be puter global isServerUnupdated

interval : saver of picks if isServerUnupdated ::: user of comp+ id (different lsKey for difrnt comp-id )

changer of picks - adder/deleter : setter of  isServerUnupdated = yes

saver 2 server : setter of isServerUnupdated = false

reloader from ls : only for new compilation (empty array) ::: user of lsKeyvwt comp-id
  */
  
  
  //update_state with compilation_id
 function updateStateWithCompilationInfo(compilation_id, compilation_items )
 {
 
 state.compilation_id = compilation_id ;
    console.log("state.compilation_id:", state.compilation_id);
    
    state.picked = compilation_items ;
    console.log("state.picked:", state.picked);
    
    
 }
  
    //updateProgressBar ie. progress-bar
  function updateProgressBar(){
    let picked = getCountOfPickedQs_noInfo() ; //state.picked.length; 
    let total = state.max_pick; //myb later - regular will less eg 40 max 
    

       $('#pickedCounter').text(picked);
  //  $('#pickedCounter').text(picked);
    $('#totalCounter').text(total);

    let pct = total === 0 ? 0 : Math.round((picked / total) * 100);
    $('#pickedProgress').css('width', pct + '%');
}

  //disable for view-only
  function disableElemsForViewOnly(){
  if (new URLSearchParams(location.search).get('userCanEdit') === 'false') {
  
    $('input, textarea, select').attr('disabled', true);
  
        // 2️⃣ switch to picked Questions tab
    var tab = new bootstrap.Tab($('#pickerTabs button[data-bs-target="#picked_pane_id"]')[0]);
    tab.show();
    $('button').hide();
}
  }

      
 // Utility: Populate a dropdown
    function populateDropdown($el, data) {
       
        $el.empty()
            .append('<option value="">-- Select --</option>')
            .append('<option value="">None</option>');
        
        
        if (data && data.length) {
            data.forEach(item => {
                const val = item;
                $el.append(`<option value="${val}">${val}</option>`);
            });
        }
    }

       
      
    // 1️⃣ On page load: get subjects for selected arm
    function loadSubjects() {
        const arm = $('#f_arm').val();
        

        $.ajax({
            url: Q_DROPDOWN_ENDPOINT,
            method: 'GET',
            data: {
                arm: arm,
                distinctColumn : 'subject'
            },
            success: function(res) {
                if (res.error) {
                    alert(res.error);
                    return;
                }
                else if(res.success) {
                    alert('Subjects loaded successfully.')
                 }
                
                populateDropdown($('#f_subject'), res.data);
            },
                error: function(xhr, status, error) {
        console.log("Status:", status);
        console.log("Error:", error);
        console.log("Response:", xhr.responseText);
    
                alert("Error fetching subjects");
            }
        });
    }    
      
  
      
/******
******
********/      
      
      
   // 2️⃣ On change of arm: reload subjects
    $('#arm').change(function() { //**>
        loadSubjects();

        // reset dependent dropdowns
        $('#f_topic, #f_year, #f_exam_body').empty().append('<option value="">-- Select --</option>');
    }); //**>

    // 3️⃣ On change of subject: load topic, year, exam_body
    $('#f_subject').change(function() { //#%%
        const arm = $('#f_arm').val();
        const subject = $(this).val();

        if (!subject) { //@@
            // reset dependent dropdowns
            $('#f_topic, #f_year, #f_exam_body').empty().append('<option value="">-- Select --</option>');
            return;
        } //@@

        // List of distinct columns to request
        const columns = ['topic', 'year', 'exam_body'];

        columns.forEach(col => { //_++
            $.ajax({ //~@
                url: Q_DROPDOWN_ENDPOINT,
                method: 'GET',
                data: {
                    arm: arm,
                    subject: subject,
                    distinctColumn : col
                },
                success: function(res) { //&&
                    if (res.error) { //%%
                        console.error(res.error);
                        return;
                    } //%%
                    else if(res.success) {
                     console.log("result",res.data)
                        }

                    // Map column to element
                    const $el = $(`#f_${col}`);
                    populateDropdown($el, res.data);
                } //&&
                ,
                error: function() { //*&
                    console.error(`Error fetching ${col}`);
                } //*&
            }); //~@
        }); //_++a
    }); //#%%




  // expose for debugging
  window.Picker = { state: state, fetchPage: fetchPage, saveLS: saveLS, shufflePicked: shufflePicked };

})(jQuery);













