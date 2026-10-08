import React, { forwardRef, InputHTMLAttributes } from 'react';
import { formatDate, formatContractDate } from '../../utils/dateFormatter';

export interface DatePickerProps extends Omit<InputHTMLAttributes<HTMLInputElement>, 'type'> {
  value?: string;
  onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onBlur?: (e: React.FocusEvent<HTMLInputElement>) => void;
  dateFormat?: 'dd-MM-yy' | 'dd-MM-yyyy';
}

export const DatePicker = forwardRef<HTMLInputElement, DatePickerProps>(
  ({ value, onChange, onBlur, className, dateFormat = 'dd-MM-yyyy', ...props }, ref) => {
    const hiddenDateRef = React.useRef<HTMLInputElement>(null);

    // Sync external ref if provided
    React.useImperativeHandle(ref, () => hiddenDateRef.current as HTMLInputElement);



    const displayValue = value ? (dateFormat === 'dd-MM-yyyy' ? formatContractDate(value) : formatDate(value)) : '';
    const placeholderText = dateFormat === 'dd-MM-yyyy' ? 'DD-MM-YYYY' : 'DD-MM-YY';

    return (
      <div className="relative w-full">
        <input
          {...props}
          name={undefined}
          type="text"
          value={displayValue}
          readOnly
          data-date-display
          className={className}
          placeholder={props.placeholder || placeholderText}
        />
        <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <input
          type="date"
          data-date-native
          name={props.name}
          aria-label={props['aria-label'] || props.placeholder || props.name || 'Choose date'}
          required={props.required}
          disabled={props.disabled}
          min={props.min}
          max={props.max}
          ref={hiddenDateRef}
          value={value || ''}
          onChange={onChange}
          onBlur={onBlur}
          onClick={(e) => {
            try {
              if ('showPicker' in HTMLInputElement.prototype) {
                (e.target as HTMLInputElement).showPicker();
              }
            } catch {
              // Ignore
            }
          }}
          className="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
          style={{ padding: 0, margin: 0, border: 'none', background: 'transparent' }}
        />
      </div>
    );
  }
);

DatePicker.displayName = 'DatePicker';
