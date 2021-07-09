export const dictionaryItemsTypes = {
  template: 'template',
  itgContainer: 'itgcontainer',
  carrier: 'carrier',
  vessel: 'vessel',
  ptImageType: 'pt-imagetype',
  ccLoadType: 'cc-loadtype',
}

export const objectLocks = {
  refreshIntervalTime: 10000,
  objectTypes: {
    request: 'request',
    order: 'order'
  },
  lockTypes: {
    claimLock: 'claim-lock',
    selectRequest: 'select-request',
    openOrder: 'open-order'
  }
}

export const statuses = {
  intakeAccepted: 'intake-accepted',
  intakeAcceptedDatafile: 'intake-accepted-datafile',
  intakeException: 'intake-exception',
  intakeRejected: 'intake-rejected',
  intakeStarted: 'intake-started',
  ocrCompleted: 'ocr-completed',
  ocrPostProcessingReview: 'ocr-post-processing-review',
  processOcrOutputFileReview: 'process-ocr-output-file-review',
  ocrPostProcessingComplete: 'ocr-post-processing-complete',
  ocrPostProcessingError: 'ocr-post-processing-error',
  ocrPostProcessingAutosubmitted: 'ocr-post-processing-autosubmited',
  replicatedFromExistingOrder: 'replicated-from-existing-order',
  ocrWaiting: 'ocr-waiting',
  ocrTimedout: 'ocr-timedout',
  processOcrOutputFileComplete: 'process-ocr-output-file-complete',
  processOcrOutputFileError: 'process-ocr-output-file-error',
  uploadRequested: 'upload-requested',

  sendingToWint: 'sending-to-wint',
  autoSendingToWint: 'auto-sending-to-wint',
  failureSendingToWint: 'failure-sending-to-wint',
  successSendingToWint: 'success-sending-to-wint',
  shipmentCreatedByWint: 'shipment-created-by-wint',
  shipmentNotCreatedByWint: 'shipment-not-created-by-wint',
  updatingToWint: 'updating-to-wint',
  failureUpdatingToWint: 'failure-updating-to-wint',
  successUpdatingToWint: 'success-updating-to-wint',
  shipmentUpdatedByWint: 'shipment-updated-by-wint',
  shipmentNotUpdatedByWint: 'shipment-not-updated-by-wint',

  sendingToChainio: 'sending-to-chainio',
  autoSendingToChainio: 'auto-sending-to-chainio',
  successSendingToChainio: 'success-sending-to-chainio',
  failureSendingToChainio: 'failure-sending-to-chainio',
  shipmentCreatedByChainio: 'shipment-created-by-chainio',
  shipmentNotCreatedByChainio: 'shipment-not-created-by-chainio',

  uploadImageRequested: 'upload-image-requested',
  uploadImageFailed: 'upload-image-failed',
  uploadImageSucceeded: 'upload-image-succeeded',

  updatesPriorOrder: 'updates-prior-order',
  requestMarkedDone: 'request-marked-done',
  requestMarkedUndone: 'request-marked-undone',
  updatedBySubsequentOrder: 'updated-by-subsequent-order',
  successImageuplodingToBlackfl: 'success-imageuploding-to-blackfl',
  failureImageuplodingToBlackfl: 'failure-imageuploding-to-blackfl',
  untriedImageuplodingToBlackfl: 'untried-imageuploding-to-blackfl'
}

export const displayStatuses = {
  processing: 'Processing',
  exception: 'Exception',
  rejected: 'Rejected',
  intake: 'Intake',
  processed: 'Processed',
  autoSubmitted: 'Auto Submitted',
  sendingToTms: 'Sending to TMS',
  sentToTms: 'Sent to TMS',
  acceptedByTms: 'Accepted by TMS',
  tmsWarning: 'TMS Warning',
  tmsError: 'TMS Error',
  needsReview: 'Needs Review',
  replication: 'Replication',
  markDone: 'Marked Complete',
  markUndone: 'Marked not Complete',
  uploadingImage: 'Uploading Image',
  imageUploadFailed: 'Image Upload Failed',
  imageUploaded: 'Image Uploaded',
}

export const metrics = {
  companyDaily: 'company-daily-report'
}

export const abbySourceFileds = {
  preset_fields: [
    'bill_comment',
    'bill_to_address',
    'booking_number',
    'carrier',
    'container_length|container_size',
    'contents',
    'customer_number',
    'cutoff_date',
    'cutoff_time',
    'equipment_type',
    'expedite',
    'fuel_surcharge',
    'hazmat',
    'house_bol_hawb',
    'line_haul',
    'load_number',
    'master_bol_mawb',
    'pickup_by_date|appointment_date',
    'pickup_by_time|appointment_time',
    'pickup_number',
    'purchase_order_number',
    'quantity',
    'reference_number',
    'seal_number',
    'ship_comment',
    'unit_number',
    'vessel',
    'voyage',
    'weight'
  ],
  old_fields: [
    'actual_destination',
    'actual_orgin',
    'aux_1',
    'aux_2',
    'aux_3',
    'aux_4',
    'bill_charge',
    'equipment_provider',
    'location',
    'location1',
    'location2',
    'rate_box',
    'total_accessorial_charges'
  ],
  new_fields: [
    'customer_location',
    'empty_location',
    'terminal_location'
  ],
}
