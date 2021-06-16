
-- php artisan metrics:companies-daily --company-id=1 --from=2021-05-01 --to=2021-05-31
-- php artisan metrics:companies-daily --company-id=1 --from=2020-07-01 --to=2021-05-31
-- php artisan metrics:companies-daily --from=2020-12-01 --to=2021-05-31

-- 0. Company Name
-- A. Name: Single-order active PDF requests Formula: Total Number of PDF Requests (excluding rejected and deleted requests) with only one order assigned to them - Number of active single-order PDF requests where the order is a revision
-- B.Name: Multi-order active PDF requests Formula: Total Number of PDF Requests (excluding rejected and deleted requests) with more than one order assigned to them - Number of active multi-order PDF requests where ALL orders are revisions
-- D.Name: Active CSV/Tabular requests Should contain: Total Number of datafile Requests (excluding rejected and deleted requests) - requests where all are revisions
-- G.Name: Total Number of Requests Should contain: All requests for the company. Formerly called “Number of requests”
-- H.Name: Additional PDF Orders. Formula: (Number of orders from pdf - number of orders that were updated before (with existing reference number) ) - number of pdf requests (excluding requests where all orders are updates)
-- I.Name: Additional CSV / Tabular Orders. Formula: (Number of orders from datafile - number of orders that were updated before (with existing reference number) ) - number of datafile requests (excluding requests where all orders are updates)
-- J.Name: PDF Order Revisions. Formula: Number pdf of orders with prior updates
-- K.Name: CSV/Tabular Order Revisions. Formula: Number datafile of orders with prior updates
-- L.Name: PDF Pages Overages. Formula: Number of pdf pages - (number of all pdf orders x 2)
-- M. Name: Number of TMS Shipments: Formula: All TMS Shipments for the Company
-- O. Name: Total Number of PDF Orders Formula: All pdf orders of the company (incl deleted and rejected)
-- P. Name: Total Number of CSV Orders Formula: All pdf orders of the company (incl deleted and rejected)
-- Q. Name: Total Number of PDF Pages Formula: All pdf pages of the company (incl deleted and rejected)


set session sql_mode='';
select
    name as company_name,
    date_format(metric_date, '%Y-%m') as metric_month,
    sum(pdf_requests_singleorder_none_updateprior) as A_pdf_requests_singleorder_none_updateprior,
    sum(pdf_requests_multiorder_less_all_updateprior) as B_pdf_requests_multiorder_less_all_updateprior,
    sum(datafile_requests_none_updateprior) as D_datafile_requests_none_updateprior,
    sum(requests) as G_requests,
    sum(requests_deleted) as requests_deleted,
    sum(orders) as orders,
    sum(orders_deleted) as orders_deleted,
    sum(pdf_orders_less_requests_anyupdateprior) as H_pdf_orders_less_requests_anyupdateprior,
    sum(datafile_orders_less_requests_anyupdateprior) as I_datafile_orders_less_requests_anyupdateprior,
    sum(pdf_orders_updateprior) as J_pdf_orders_updateprior,
    sum(datafile_orders_updateprior) as K_datafile_orders_updateprior,
    if(metric_date < '2021-03-01', 'na', sum(pdf_pages_overage)) as L_pdf_pages_overages,
    sum(tms_shipments) as M_tms_shipments,
    sum(pdf_orders_including_deleted) as O_pdf_orders_including_deleted,
    sum(datafile_orders_including_deleted) as P_datafile_orders_including_deleted,
    if(metric_date < '2021-03-01', 'na', sum(pdf_pages_including_deleted)) as Q_pdf_pages_including_deleted
from t_company_daily_metrics
join t_companies on t_company_id = t_companies.id
group by t_company_id, metric_month
having true
  and (G_requests + requests_deleted + orders + orders_deleted) > 0
  and name not like '%onboarding%'
  and name not like '%demo%'
order by name, metric_month
;