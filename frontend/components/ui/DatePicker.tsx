import React, { useState, forwardRef, InputHTMLAttributes } from 'react';
import { formatDate } from '../../utils/dateFormatter';

export interface DatePickerProps extends Omit<InputHTMLAttributes<HTMLInputElement>, 'type'> {
  value?: string;
  onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onBlur?: (e: React.FocusEvent<HTMLInputElement>) => void;
}

export const DatePicker = forwardRef<HTMLInputElement, DatePickerProps>(
  ({ value, onChange, onBlur, className, ...props }, ref) => {
    const [focused, setFocused] = useState(false);

    if (focused) {
      return (
        <input
          {...props}
          type="date"
          ref={ref}
          value={value || ''}
          onChange={onChange}
          onBlur={(e) => {
            setFocused(false);
            onBlur?.(e);
          }}
          autoFocus
          className={className}
        />
      );
    }

    // Display formatted value when not focused
    const displayValue = value ? formatDate(value) : '';

    return (
      <input
        {...props}
        type="text"
        value={displayValue}
        onFocus={() => setFocused(true)}
        readOnly
        className={className}
        placeholder="DD-MM-YY"
      />
    );
  }
);

DatePicker.displayName = 'DatePicker';
