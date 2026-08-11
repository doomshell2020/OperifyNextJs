const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_quotations', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    quotation_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    vendor_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    selected_bid_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    is_award: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "N"
    },
    vendorshipaddress: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    delivery_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    acceptance_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    freight: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    payment_terms: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    transit_insurance: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    remark: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    email_vendor: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    total_qty: {
      type: DataTypes.FLOAT,
      allowNull: false
    },
    total_tax: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    total_amt: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    is_revised: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0,
      comment: "PO revised count"
    },
    status: {
      type: DataTypes.ENUM('Y','N','R'),
      allowNull: false,
      defaultValue: "Y",
      comment: "R for check PO is revised or not"
    },
    added_by: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    added_by_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    added_time: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    updated_by: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    updated_by_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    updated_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    token: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    amendment_remarks: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    issue_vendor: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    payment_term: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    postatus: {
      type: DataTypes.ENUM('O','C'),
      allowNull: true,
      defaultValue: "O"
    },
    revised_date: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'st_quotations',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
      {
        name: "id",
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
